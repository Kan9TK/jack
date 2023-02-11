<?php

declare(strict_types=1);

namespace kang\trashcan\command;


use CortexPE\Commando\BaseCommand;
use kang\trashcan\inventory\TrashCanInventory;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class TrashCanCommand extends BaseCommand{

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void{
        if ( ! $sender instanceof Player ) return;
        $inv = new TrashCanInventory();
        $inv->sendTo($sender);
    }

    protected function prepare(): void{
        $this->setPermission("true");
    }

}