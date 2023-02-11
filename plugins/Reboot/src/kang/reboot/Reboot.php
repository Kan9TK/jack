<?php

declare(strict_types=1);

namespace kang\reboot;

use kang\dataconfig\DataConfig;
use kang\reboot\command\RebootCommand;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\Internet;

class Reboot extends PluginBase{

    protected static Reboot $instance;

    protected DataConfig $config;

    protected int $enableTime = 0;
    protected int $rebootTick = 60;

    public static function getInstance() : Reboot{
        return self::$instance;
    }

    protected function onLoad(): void{
        self::$instance = $this;
    }

    protected function onEnable(): void
    {

        $this->config = new DataConfig($this->getDataFolder()."config.json",DataConfig::TYPE_JSON,["delay"=>60*60*2]);
        $this->enableTime = time();

        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function() : void{
            if ( time() >= $this->enableTime+$this->config->get("delay") ){
                $this->start();
            }
        }), 20*60);

        $this->getServer()->getCommandMap()->registerAll($this->getName(),[
            new RebootCommand($this, "재부팅", "재부팅 명령어예요.")
        ]);
    }

    public function setDelay(int $time) : void{
        $this->config->set("delay", $time*60*60);
        $this->enableTime = time();
    }

    public function getLeftTime() : int{
        return $this->enableTime+$this->config->get("delay") - time();
    }

    public function start() : void{
        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function() : void{
            if ( $this->rebootTick == 0 ){
                foreach($this->getServer()->getOnlinePlayers() as $player){
                    $player->save();
                }
                foreach($this->getServer()->getWorldManager()->getWorlds() as $level){
                    $level->save(true);
                }
                $this->getServer()->shutdown();
            }else{
                $this->getServer()->broadcastTitle("§c§l!", $this->rebootTick--."초 후 서버가 재부팅됩니다.");
            }

        }), 20);
    }

}