<?php

/**
 * @name CancelDecay
 * @author kang
 * @version 1.0.0
 * @api 4.0.0
 * @main canceldecay\CancelDecay
 */

namespace canceldecay;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\block\LeavesDecayEvent;

class CancelDecay extends PluginBase implements Listener
{
    public function onEnable() : void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onLeavesDecay(LeavesDecayEvent $event){
        if (!in_array($event->getBlock()->getPosition()->getWorld(), ["wild", "town"])) {
            $event->cancel();
        }
    }

}