<?php

/**
 * @name ShowItemCode
 * @author kang
 * @version 1.0.0
 * @api 4.0.0
 * @main showitemcode\ShowItemCode
 */

namespace showitemcode;

use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;

class ShowItemCode extends PluginBase implements Listener
{
    public function onEnable() : void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onInteract(PlayerInteractEvent $event){
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $item = $player->getInventory()->getItemInHand();
        if($item->getId()==284){
            $player->sendPopup($block->getId().":".$block->getMeta());
        }
    }

}