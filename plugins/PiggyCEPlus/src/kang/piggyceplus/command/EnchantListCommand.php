<?php

declare(strict_types=1);

namespace kang\piggyceplus\command;

use CortexPE\Commando\BaseCommand;
use kang\piggyceplus\inventory\EnchantListInventory;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class EnchantListCommand extends BaseCommand{

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ( ! $sender instanceof Player ) return;
        $inv = new EnchantListInventory($sender);
        $inv->send();
    }

    protected function prepare(): void
    {
        $this->setPermission("true");
    }

}
