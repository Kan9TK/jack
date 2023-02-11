<?php

declare(strict_types=1);

namespace kang\simpletpa\command;

use CortexPE\Commando\BaseCommand;
use kang\ServerUtils\ServerUtils;
use kang\simpletpa\SimpleTpa;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class TeleportAcceptCommand extends BaseCommand{

    protected function prepare(): void
    {
        $this->setPermission("true");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ( ! $sender instanceof Player ) return;

        $plugin = SimpleTpa::getInstance();
        if ( ! $plugin->acceptTeleport($sender) ){
            ServerUtils::error($sender, "티피 요청이 존재하지 않아요.");
        }
    }

}