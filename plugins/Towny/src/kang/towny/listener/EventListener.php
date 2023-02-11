<?php

declare(strict_types=1);

namespace kang\towny\listener;

use kang\towny\setting\Setting;
use kang\towny\Towny;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;

class EventListener implements Listener{

    protected array $move = [];

    public function __construct(private Towny $plugin){}

    public function onBlock(PlayerInteractEvent|BlockPlaceEvent|BlockBreakEvent|SignChangeEvent $event) : void{
        $player = $event->getPlayer();

        if ( $player->getWorld()->getFolderName() === Setting::WORLD_NAME ) {

            $block = $event->getBlock();
            $townManager = $this->plugin->getTownManager();

            if (($town = $townManager->getTownByPos($block->getPosition())) !== null) {
                if (!$town->isMember($player->getName())) {
                    $player->sendTip("해당 땅은 ".$player->getName()."님의 마을이 아니에요.");
                    $event->cancel();
                }
            }

        }
    }

    public function onMove(PlayerMoveEvent $event) : void{
        $player = $event->getPlayer();

        if ( $event->getFrom()->equals($event->getTo()) ) return;

        if ( $player->getWorld()->getFolderName() !== Setting::WORLD_NAME ) return;

        $townManager = $this->plugin->getTownManager();
        if ( ($town = $townManager->getTownByPos($player->getPosition())) !== null ){
            if ( ($this->move[$player->getName()] ?? "") !== $town->getName() ){
                $player->sendTitle($town->getName()." 마을");
                $this->move[$player->getName()] = $town->getName();
            }
        }else{
            if ( isset ( $this->move[$player->getName()] ) ){
                unset($this->move[$player->getName()]);
                $player->sendTitle("야생");
            }
        }
    }

}