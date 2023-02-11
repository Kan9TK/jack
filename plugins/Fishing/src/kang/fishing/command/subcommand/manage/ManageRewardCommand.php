<?php

declare(strict_types=1);

namespace kang\fishing\command\subcommand\manage;

use CortexPE\Commando\BaseSubCommand;
use kang\fishing\inventory\RewardInventory;
use pocketmine\command\CommandSender;

class ManageRewardCommand extends BaseSubCommand{

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $inv = new RewardInventory();
        $inv->sendTo($sender);
    }

    protected function prepare(): void
    {
        $this->setPermission("op");
    }

}
