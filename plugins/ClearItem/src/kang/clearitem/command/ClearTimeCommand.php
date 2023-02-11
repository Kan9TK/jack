<?php

declare(strict_types=1);

namespace kang\clearitem\command;

use CortexPE\Commando\BaseCommand;
use kang\clearitem\ClearItem;
use kang\ServerUtils\ServerUtils;
use pocketmine\command\CommandSender;

class ClearTimeCommand extends BaseCommand{

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        ServerUtils::msg($sender, "청소까지 ".ClearItem::getInstance()->getTime()." 남았어요.");
    }

    protected function prepare(): void
    {
        $this->setPermission("true");
    }

}