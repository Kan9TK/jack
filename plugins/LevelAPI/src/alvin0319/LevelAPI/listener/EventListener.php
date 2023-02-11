<?php

declare(strict_types=1);

namespace alvin0319\LevelAPI\listener;

use alvin0319\LevelAPI\LevelAPI;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerRespawnEvent;

class EventListener implements Listener{

    public function __construct(private LevelAPI $plugin){}

    public function onPlayerJoin(PlayerJoinEvent $event) : void{
        $player = $event->getPlayer();

        if(!$this->plugin->hasData($player)){
            $this->plugin->createData($player);
        }

        //$this->plugin->setXpAndProgress($player);
    }

    public function onPlayerDeath(PlayerDeathEvent $event) : void{
        $event->setXpDropAmount(0);
    }

    public function onPlayerRespawn(PlayerRespawnEvent $event) : void{
        $player = $event->getPlayer();

        /*
        $this->getScheduler()->scheduleTask(new ClosureTask(function() use ($player) : void{
            $this->setXpAndProgress($player);
        }));
        */
    }

}