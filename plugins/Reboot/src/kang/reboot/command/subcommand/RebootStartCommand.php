<?php

declare(strict_types=1);

namespace kang\reboot\command\subcommand;

use CortexPE\Commando\BaseSubCommand;
use kang\reboot\Reboot;
use kang\ServerUtils\ServerUtils;
use pocketmine\command\CommandSender;

class RebootStartCommand extends BaseSubCommand{

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        Reboot::getInstance()->start();
    }

    protected function prepare(): void
    {
        $this->setPermission("op");
    }

}