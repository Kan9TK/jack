<?php

declare(strict_types=1);

namespace kang\mailbox\listener;

use kang\mailbox\MailBox;
use kang\ServerUtils\ServerUtils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class EventListener implements Listener{

    public function onJoin(PlayerJoinEvent $event) : void{
        $player = $event->getPlayer();
        $beforeCount = MailBox::getAllItemCount($player);
        MailBox::checkExpire($player);
        $afterCount = MailBox::getAllItemCount($player);

        $expireCount = $afterCount-$beforeCount;

        if ( $afterCount > 0 ){
            $expireMsg = $expireCount>0 ? ("(".$expireCount."개의 아이템이 만료되었어요.)") : "";
            ServerUtils::msg($player, "우편함에 총 ".$afterCount."개의 아이템이 존재해요.".$expireMsg);
        }
    }

}