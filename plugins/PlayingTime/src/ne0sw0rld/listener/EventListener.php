<?php

namespace ne0sw0rld\listener;

use ne0sw0rld\PlayingTime;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;

class EventListener implements Listener
{

    public function __construct(private PlayingTime $plugin){}

	public function onJoin(PlayerJoinEvent $event) : void{
		$name = strtolower ($event->getPlayer()->getName());
        $this->plugin->time[$name] = time();
	}
	
	public function onQuit(PlayerQuitEvent $event) : void{
		$name = strtolower ($event->getPlayer()->getName());
		$this->plugin->updateTime($name);
		unset($this->plugin->time[$name]);
	}


}