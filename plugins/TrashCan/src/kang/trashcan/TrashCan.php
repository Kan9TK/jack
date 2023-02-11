<?php

declare(strict_types=1);

/**
 * @name TrashCan
 * @author kang
 * @version 1.0.0
 * @api 4.0.0
 * @main kang\trashcan\TrashCan
 */

namespace kang\trashcan;

use kang\trashcan\command\ClearInventoryCommand;
use kang\trashcan\command\TrashCanCommand;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\plugin\PluginBase;

class TrashCan extends PluginBase{

    protected function onEnable(): void{
        $this->getServer()->getCommandMap()->registerAll($this->getName(), [
            new TrashCanCommand($this, "쓰레기통", "쓰레기통 명령어예요."),
            new ClearInventoryCommand($this, "인벤청소", "인벤토리 청소 명령어예요.")
        ]);
        if ( ! InvMenuHandler::isRegistered() ){
            InvMenuHandler::register($this);
        }
    }

}