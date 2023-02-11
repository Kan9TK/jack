<?php

declare(strict_types=1);

namespace kang\clearitem;

use kang\clearitem\command\ClearItemCommand;
use kang\ServerUtils\ServerUtils;
use MySetting\MySetting;
use MySetting\Setting;
use pocketmine\entity\object\ExperienceOrb;
use pocketmine\entity\object\ItemEntity;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\sound\PopSound;

class ClearItem extends PluginBase{

    protected static ClearItem $instance;

    public const CLEAR_TIME = 60*30; // 30분

    protected int $tick = self::CLEAR_TIME;
    protected array $noticeTimes = [60,30,10,9,8,7,6,5,4,3,2,1];

    public static function getInstance() : ClearItem{
        return self::$instance;
    }

    protected function onLoad() : void{
        self::$instance = $this;
    }

    protected function onEnable(): void
    {

        $this->getScheduler()->scheduleTask(new ClosureTask(function() : void{
        }));

        $this->getServer()->getCommandMap()->registerAll($this->getName(), [
            new ClearItemCommand($this, "청소", "아이템을 청소해요."),
            new ClearItemCommand($this, "청소시간", "청소까지 남은 시간을 확인해요.")
        ]);

        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function() : void{
            if ( in_array($this->tick, $this->noticeTimes) ){
                foreach ( $this->getServer()->getOnlinePlayers() as $player ){
                    if(($setting = MySetting::getInstance()->getSetting($player)) instanceof Setting) {
                        if ($setting->getSetting(Setting::SETTINGS_ALERT_CLEANER)){
                            $player->getWorld()->addSound($player->getPosition(), new PopSound(), [$player]);
                            $player->sendTip($this->tick."초 후 땅에 떨어진 아이템이 청소돼요!");
                        }
                    }
                }
            }

            if ( $this->tick-- > 0 ) return;

            $c = 0;
            foreach ( $this->getServer()->getWorldManager()->getWorlds() as $world ){
                foreach ( $world->getEntities() as $entity ){
                    if ( $entity instanceof ItemEntity ){
                        $entity->close();
                        $c++;
                    }
                    if ( $entity instanceof ExperienceOrb ){
                        $entity->close();
                        $c++;
                    }
                }
            }
            $this->getServer()->broadcastTip($c."개의 아이템이 청소되었어요!");
            $this->tick = self::CLEAR_TIME;
        }), 20);
    }

    public function getTime() : string{
        return ServerUtils::TimeToString($this->tick);
    }

    public function setTime(int $time) : void{
        $this->tick = $time;
    }

}