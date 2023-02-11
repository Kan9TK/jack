<?php

declare(strict_types=1);

/**
 * @name JQMsg
 * @author kang\jqmsg
 * @version 1.0.0
 * @api 4.0.0
 * @main kang\jqmsg\JQMsg
 */

namespace kang\jqmsg;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;

class JQMsg extends PluginBase implements Listener {

    protected function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onJoin(PlayerJoinEvent $event) : void{
        $event->setJoinMessage("");
        $this->getServer()->broadcastTip("§a+§r ".$event->getPlayer()->getName()." [".count($this->getServer()->getOnlinePlayers())."명]");
    }

    public function onQuit(PlayerJoinEvent $event) : void{
        $event->setJoinMessage("");
        $this->getServer()->broadcastTip("§c-§r ".$event->getPlayer()->getName()." [".count($this->getServer()->getOnlinePlayers())."명]");
    }

}