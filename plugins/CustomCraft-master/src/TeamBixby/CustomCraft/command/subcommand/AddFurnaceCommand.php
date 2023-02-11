<?php

declare(strict_types=1);

namespace TeamBixby\CustomCraft\command\subcommand;

use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use TeamBixby\CustomCraft\inventory\AddFurnaceInventory;

class AddFurnaceCommand extends BaseSubCommand{

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $inv = new AddFurnaceInventory();
        $inv->sendTo($sender);
    }

    protected function prepare(): void
    {
        $this->setPermission("op");
    }

}