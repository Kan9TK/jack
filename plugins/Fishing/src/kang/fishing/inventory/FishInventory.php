<?php

declare(strict_types=1);

namespace kang\fishing\inventory;

use kang\fishing\fish\Fish;
use kang\fishing\fish\FishManager;
use kang\fishing\Fishing;
use kang\fishing\task\GetAllTask;
use kang\fishing\task\SellAllTask;
use kang\ServerUtils\ServerUtils;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use onebone\economyapi\EconomyAPI;
use pocketmine\block\BlockLegacyIds as BlockIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

class FishInventory{

    public const TYPE_SELL = 0;
    public const TYPE_GET = 1;

    protected InvMenu $inv;

    protected int $page = 0;
    protected int $totalPrice = 0;
    protected int $totalCount = 0;

    public function __construct(protected Player $player, protected $type = self::TYPE_SELL){
        $this->inv = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $this->inv->setName("물고기 보관함");
        $this->inv->setListener(\Closure::fromCallable([$this, "onTransaction"]));
        $this->inv->setInventoryCloseListener(\Closure::fromCallable([$this, "onClose"]));

        $this->setting();

    }

    public function onTransaction(InvMenuTransaction $action) : InvMenuTransactionResult{
        $item = $action->getOut();
        if($item->getNamedTag()->getTag("previous") !== null){
            if ( $this->page > 0 ){
                $this->page--;
                $this->setting();
            }
            return $action->discard();
        }
        if($item->getNamedTag()->getTag("next") !== null){
            $this->page++;
            $this->setting();
            return $action->discard();
        }
        if($item->getNamedTag()->getTag("fish") !== null){

            $fishManager = Fishing::getInstance()->getFishManager();
            $slot = $action->getAction()->getSlot();
            $fish = $fishManager->getFishByIndex($this->player, $slot + $this->page * 45);

            switch ( $this->type ){
                case self::TYPE_SELL:
                    $price = $fishManager->getFishPrice($fish);
                    EconomyAPI::getInstance()->addMoney($this->player, $price);

                    $fishManager->deleteFish($this->player, $slot + $this->page * 45);
                    $this->inv->getInventory()->setItem($slot, VanillaItems::AIR());
                    $this->totalPrice += $price;
                    $this->totalCount++;
                    break;
                case self::TYPE_GET:
                    $this->player->getInventory()->addItem($fish->asItem());

                    $fishManager->deleteFish($this->player, $slot + $this->page * 45);
                    $this->inv->getInventory()->setItem($slot, VanillaItems::AIR());
                    break;
            }
        }
        if($item->getNamedTag()->getTag("reload") !== null){
            $this->setting();
        }
        if($item->getNamedTag()->getTag("mode") !== null){
            $this->type = $this->type == self::TYPE_SELL? self::TYPE_GET : self::TYPE_SELL;
            $this->setting();
        }
        if($item->getNamedTag()->getTag("sellall") !== null){
            $fishManager = Fishing::getInstance()->getFishManager();
            switch ( $this->type ){
                case self::TYPE_SELL:
                    $this->player->removeCurrentWindow();
                    $fishManager->sellAllFish($this->player);
                    break;
                case self::TYPE_GET:
                    $this->player->removeCurrentWindow();
                    $fishManager->getAllFish($this->player);
                    break;
            }
        }
        return $action->discard();
    }

    public function onClose() : void{
        if ( $this->totalPrice !== 0 ) {
            ServerUtils::msg($this->player, "총 ".$this->totalCount."마리의 물고기를 판매하여 ".$this->totalPrice."원을 획득했어요.");
        }
    }

    public function setting() : void{
        $this->inv->getInventory()->clearAll();
        $this->settingItems();
        $this->settingButtons();
    }

    public function settingItems() : void{
        foreach ( Fishing::getInstance()->getFishManager()->getFishes($this->player) as $index => $fish ){
            if ($index >= $this->page * 45 && $index <= ($this->page + 1) * 45) {
                if ($fish instanceof Fish) {
                    $item = $fish->asItem();
                    $lore = $item->getLore();
                    if ( $this->type == self::TYPE_SELL ){
                        $lore[] = "\n§r§b끌어오거나 클릭하여 판매";
                        $item->setLore($lore);
                    }else{
                        $lore[] = "\n§r§b끌어오거나 클릭하여 가져오기";
                        $item->setLore($lore);
                    }
                    $item->getNamedTag()->setString("fish","");
                    $this->inv->getInventory()->setItem($index - $this->page * 45, $item);
                }
            }
        }
    }

    public function settingButtons() : void{
        $inv = $this->inv->getInventory();
        $fishManager = Fishing::getInstance()->getFishManager();

        $item = ItemFactory::getInstance()->get(BlockIds::BED_BLOCK, 0, 1)
            ->setCustomName("§r새로 고침");
        $item->getNamedTag()->setString("reload", "");
        $inv->setItem(45, $item);

        $item = ItemFactory::getInstance()->get(BlockIds::BED_BLOCK, 0, 1)
            ->setCustomName("§r모드 변경")
            ->setLore([
                "§r현재 모드: ".($this->type==self::TYPE_GET ? "§r물고기 가져오기" : "§r물고기 판매"),
                $this->type==self::TYPE_GET ? "§r물고기 판매로 모드를 변경해요." : "§r물고기 가져오기로 모드를 변경해요."
            ]);
        $item->getNamedTag()->setString("mode", "");
        $inv->setItem(46, $item);

        $item = ItemFactory::getInstance()->get(ItemIds::PAPER, 0, 1)
            ->setCustomName("§r이전 페이지")
            ->setLore(["§r현재 페이지: ".($this->page+1)]);
        $item->getNamedTag()->setString("previous", "");
        $inv->setItem(48, $item);

        $item = VanillaBlocks::CHEST()->asItem()
            ->setCustomName("§r보관 현황");
        $lore = $item->getLore();
        foreach ( FishManager::LENGTH_TYPE_LIST as $type ){
            $lore[] = "§r".FishManager::LENGTH_TYPE_COLOR_LIST[$type]."§r - ".count($fishManager->getFishesByType($this->player, $type))."마리";
        }
        $lore[] = "\n§r총 ".count($fishManager->getFishes($this->player))."마리";
        $item->setLore($lore);
        $item->getNamedTag()->setString("info", "");
        $inv->setItem(49, $item);

        $item = ItemFactory::getInstance()->get(ItemIds::PAPER, 0, 1)
            ->setCustomName("§r다음 페이지")
            ->setLore(["§r현재 페이지: ".($this->page+1)]);
        $item->getNamedTag()->setString("next", "");
        $inv->setItem(50, $item);

        $item = ItemFactory::getInstance()->get(BlockIds::BED_BLOCK, 0, 1)
            ->setCustomName("§r물고기 가격표");
        $lore = $item->getLore();
        foreach ( FishManager::PRICE_TYPE as $type => $price ){
            $lore[] = "§r".FishManager::LENGTH_TYPE_COLOR_LIST[$type]."§r - ".$price."원";
        }
        $item->setLore($lore);
        $item->getNamedTag()->setString("priceList", "");
        $inv->setItem(52, $item);
        
        $item = ItemFactory::getInstance()->get(BlockIds::BED_BLOCK, 0, 1)
            ->setCustomName("§r전체 ".($this->type==self::TYPE_SELL ? "판매" : "가져오기"));
        $item->getNamedTag()->setString("sellall", "");
        $inv->setItem(53, $item);
    }

    public function sendTo(Player $player) : void{
        $this->inv->send($player);
    }

}