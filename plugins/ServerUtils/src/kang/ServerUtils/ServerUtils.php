<?php

declare(strict_types=1);

namespace kang\ServerUtils;

use kang\ServerUtils\task\ArsortTask;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\world\sound\EndermanTeleportSound;
use skymin\asyncqueue\AsyncQueue;
use kang\ServerUtils\task\RankingTask;

function convert(Player|string $player) : string{
    return strtolower($player instanceof Player ? $player->getName() : $player);
}

class ServerUtils extends PluginBase{

    protected function onLoad() : void{
        date_default_timezone_set("Asia/Seoul");
    }

    protected function onEnable() : void{
        $this->getScheduler()->scheduleTask(new ClosureTask(function() : void{
            foreach($this->getServer()->getPluginManager()->getPlugins() as $plugin){
                try{
                    $reflection = new \ReflectionClass($plugin);
                    if($reflection->hasProperty("prefix")){
                        $property = $reflection->getProperty("prefix");
                        if(!$property->isPublic()){
                            $property->setAccessible(true);
                        }
                        $property->setValue($plugin, "§b§l! §r§7");
                    }elseif($reflection->getStaticPropertyValue("prefix", "") !== ""){
                        $reflection->setStaticPropertyValue("prefix", "§b§l! §r§7");
                    }
                }catch(\ReflectionException $e){
                    continue;
                }
            }
        }));
    }

    public static function msg(Player|CommandSender $sender, string $text){
        $sender->sendMessage("§b§l! §r§7" . $text);
    }

    public static function error(Player|CommandSender $sender, string $text){
        $sender->sendMessage("§c§l! §r§7" . $text);
    }

    public static function broad(string $text){
        Server::getInstance()->broadcastMessage("§e§l! §r§f" . $text);
    }

    public static function getArsortTask(array $array) : ArsortTask{
        return new ArsortTask($array);
    }

    public static function sendRanking(Player|CommandSender $player, array $data, string $prefix = "", $page = 1, int $cutCount = 5, string $unit = "", ?callable $valueCallable = null) : void{

        $max = count($data);
        $maxPage = ceil($max/$cutCount);

        $page = min($maxPage, $page);
        $page = max(1, $page);

        $offset = ($page * $cutCount) - ($cutCount-1) - 1;
        $length = $cutCount;

        $task = new RankingTask($data, $offset, $length, strtolower($player->getName()));

        $callable = function(RankingTask $task) use($player, $prefix, $page, $cutCount, $max, $maxPage, $unit, $valueCallable) : void {

            $server = Server::getInstance();

            $i = 0;
            $player->sendMessage('§6§l<===== §f'.$prefix.' 순위 §6§l| §r§f' . $page . ' §6§l/ §r§f' . $maxPage . ' §6§l=====>§r');
            foreach ( $task->getResult() as $playerName => $value ){
                $i++;
                $rank = ($page - 1) * $cutCount + $i;
                $line = "§l§6[§f".$rank."위§6] §r";
                $line .= $server->getNameBans()->isBanned($playerName) ? "§c[차단됨] " : '';
                $line .= $server->getInstance()->isOp($playerName) ? "§a[관리자] " : '';
                $line .= "§r" . $playerName . " : " .(is_callable($valueCallable) ? $valueCallable($value) : $value).$unit;
                $player->sendMessage($line);
            }
            if($task->playerRank===0)return;
            $player->sendMessage("§7내 순위는 전체 ".$max."명 중 ".$task->playerRank."위입니다.");

        };
        AsyncQueue::submit($task, $callable);
    }

    public static function TimeToString(?int $value, bool $minutes = true, bool $seconds = true) : string{
        if($value === null)
            return "0분";
        $h = (int) ($value / 60 / 60);
        $m = ((int) ($value / 60)) - ($h * 60);
        $s = (int) $value - (($h * 60 * 60) + ($m * 60));

        $str = "";

        if($h > 0)
            $str .= $h . "시간 ";
        if($minutes && $m > 0)
            $str .= $m . "분 ";
        if($seconds)
            $str .= $s . "초";

        return $str;
    }

    public static function addTeleportSound(Player $player) : void{
        $player->getWorld()->addSound($player->getPosition(), new EndermanTeleportSound());
    }

}