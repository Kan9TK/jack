<?php

declare(strict_types=1);

namespace kang\piggyceplus\listener;

use kang\piggyceplus\inventory\ExtractSelectItemInventory;
use kang\piggyceplus\inventory\SlotSelectItemInventory;
use kang\piggyceplus\util\CEBookUtil;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;

class EventListener implements Listener{

    public function onSneak(PlayerToggleSneakEvent $event) : void{
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();

        if ( $item->getNamedTag()->getTag(CEBookUtil::COUPON_TAG) !== null ){

            if ( $player->getCurrentWindow() === null ) {

                switch ( $item->getNamedTag()->getTag(CEBookUtil::COUPON_TAG)->getValue() ){

                    case CEBookUtil::COUPON_EXTRACT:
                        $inv = new ExtractSelectItemInventory($player);
                        $inv->send();
                        break;
                    case CEBookUtil::COUPON_SLOT:
                        $inv = new SlotSelectItemInventory($player);
                        $inv->send();
                        break;
                }

            }

        }
    }

}