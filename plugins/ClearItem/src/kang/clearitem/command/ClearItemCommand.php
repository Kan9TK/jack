<?php

declare(strict_types=1);

namespace kang\clearitem\command;

use CortexPE\Commando\BaseCommand;
use kang\clearitem\ClearItem;
use kang\ServerUtils\ServerUtils;
use pocketmine\command\CommandSender;

class ClearItemCommand extends BaseCommand{

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        ClearItem::getInstance()->setTime(10);
        ServerUtils::msg($sender, "성공적으로 청소의 남은 시간을 10초로 변경했어요.");
    }

    protected function prepare(): void
    {
        $this->setPermission("true");
    }

}