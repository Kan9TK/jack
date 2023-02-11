<?php

declare(strict_types=1);

namespace kang\mailbox;

use kang\mailbox\command\MailBoxCommand;
use kang\mailbox\listener\EventListener;
use kang\ServerUtils\ServerUtils;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

function convert($player) : string{
    return $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
}

class MailBox extends PluginBase{

    protected static array $mails = [];
    public const EXPIRE_DAY = 7;

    public function onEnable(): void{

        if(!is_dir($this->getDataFolder()."mails")){
            mkdir($this->getDataFolder()."mails");
        }
        foreach(array_diff(scandir($this->getDataFolder()."mails/"), [".", ".."]) as $file) {
            $content = file_get_contents($this->getDataFolder() . "mails/" . $file);
            $data = json_decode($content, true);
            $playerName = str_replace(".json","",$file);
            self::$mails[$playerName] = $data;
        }

        $this->getServer()->getCommandMap()->registerAll($this->getName(), [
            new MailBoxCommand($this, "우편함", "우편함 명령어예요.")
        ]);
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
    }

    public function onDisable() : void{
        foreach (self::$mails as $playerName => $items) {
            $content = json_encode($items, JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING);
            file_put_contents($this->getDataFolder() . "mails/" . $playerName . ".json", $content);
        }
    }

    public static function createData($player){
        if ( ! isset ( self::$mails[convert($player)] ) ){
            self::$mails[convert($player)] = [];
        }
    }

    public static function addItem(Player $player, Item $item){
        if ( ! $player->getInventory()->canAddItem($item) ){
            self::addMail($player, $item, "인벤토리 부족");
            ServerUtils::msg($player, "인벤토리가 부족하여 메일함으로 이동되었어요.");
        }else{
            $player->getInventory()->addItem($item);
        }
    }

    public static function addMail($player, Item $item, ?string $reason = null){
        self::createData($player);
        if ( $item->getCount() > $item->getMaxStackSize() ){
            self::addBigMail($player, $item, $reason);
            return;
        }
        self::$mails[convert($player)][] = [
            "nbt"=>$item->jsonSerialize(),
            "expire"=>strtotime("+".self::EXPIRE_DAY." day"),
            "reason"=>$reason
        ];
    }

    public static function addBigMail($player, Item $item, ?string $reason = null){
        $j = ceil($item->getCount() / $item->getMaxStackSize());
        $leftCount = $item->getCount();
        for($i=0;$i<$j;$i++){
            if ( $leftCount > $item->getMaxStackSize() ){
                $count = $item->getMaxStackSize();
            }else{
                $count = $leftCount;
            }
            self::$mails[convert($player)][] = [
                "nbt"=>$item->setCount($count)->jsonSerialize(),
                "expire"=>strtotime("+".self::EXPIRE_DAY." day"),
                "reason"=>$reason
            ];
            $leftCount -= $item->getMaxStackSize();
        }
    }

    public static function removeItem($player, int $index){
        if(isset(self::$mails[convert($player)][$index])){
            unset(self::$mails[convert($player)][$index]);
        }
    }

    public static function getItem($player, $index) : ?Item{
        self::createData($player);
        if ( ! isset ( self::$mails[convert($player)][$index] ) ){
            return null;
        }
        return Item::jsonDeserialize(self::$mails[convert($player)][$index]["nbt"]);
    }

    public static function getItems($player){
        self::createData($player);
        return self::$mails[convert($player)];
    }

    public static function checkExpire($player){
        foreach ( self::$mails[convert($player)] as $index => $itemData ){
            if ( time() >= $itemData["expire"] ){
                unset(self::$mails[convert($player)][$index]);
            }
        }
    }

    public static function getAllItemCount($player) : int{
        $c = 0;
        foreach ( self::getItems($player) as $itemData ){
            $c+= ($itemData["nbt"]["count"] ?? 1);
        }
        return $c;
    }

    public static function getAllItem(Player $player) : void{
        $c=0;
        $empty = false;
        $canAdd = false;
        foreach ( self::$mails[convert($player)] as $index => $itemData ){
            if ( $player->getInventory()->firstEmpty() == -1 ){
                $empty = true;
                break;
            }
            $item = Item::jsonDeserialize($itemData["nbt"]);
            if ( ! $player->getInventory()->canAddItem($item) ){
                $canAdd = true;
                continue;
            }
            self::removeItem($player, $index);
            $player->getInventory()->addItem($item);
            $c+=$item->getCount();
        }
        ServerUtils::msg($player, "총 ".$c."개의 아이템을 가져왔어요.");
        if ($empty or $canAdd){
            ServerUtils::msg($player, "인벤토리가 부족하여 나머지는 가져오지 못했어요.");
        }
    }

}