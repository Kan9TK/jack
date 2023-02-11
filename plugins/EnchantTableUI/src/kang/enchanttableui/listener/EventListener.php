<?php

declare(strict_types=1);

namespace kang\enchanttableui\listener;

use kang\enchanttableui\form\EnchantMainForm;
use pocketmine\block\EnchantingTable;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;

class EventListener implements Listener{

    public function onInteract(PlayerInteractEvent $event) : void{
        $player = $event->getPlayer();
        $block = $event->getBlock();
        if ( $block instanceof EnchantingTable ){
            $player->sendForm(new EnchantMainForm($player, $block));
            $event->cancel();
        }
    }

}