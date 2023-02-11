<?php

declare(strict_types=1);

namespace kang\fishing\command\subcommand;

use CortexPE\Commando\BaseSubCommand;
use kang\fishing\inventory\FishInventory;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class FishingBoxCommand extends BaseSubCommand{

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!$sender instanceof Player)return;
        $inv = new FishInventory($sender);
        $inv->sendTo($sender);
    }

    protected function prepare(): void
    {
        $this->setPermission("true");
    }

}