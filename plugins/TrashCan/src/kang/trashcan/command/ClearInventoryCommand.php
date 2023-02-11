<?php

declare(strict_types=1);

namespace kang\trashcan\command;


use CortexPE\Commando\BaseCommand;
use kang\trashcan\form\ClearInventoryForm;
use kang\trashcan\inventory\TrashCanInventory;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class ClearInventoryCommand extends BaseCommand{

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void{
        if ( ! $sender instanceof Player ) return;
        $sender->sendForm(new ClearInventoryForm());
    }

    protected function prepare(): void{
        $this->setPermission("true");
    }

}