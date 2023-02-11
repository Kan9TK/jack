<?php

declare(strict_types=1);

namespace kang\piggyceplus\command;

use CortexPE\Commando\BaseCommand;
use kang\piggyceplus\command\subcommand\GiveBookCommand;
use kang\piggyceplus\command\subcommand\GiveCouponCommand;
use pocketmine\command\CommandSender;

class ManageCECommand extends BaseCommand{

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        // TODO: Implement onRun() method.
    }

    protected function prepare(): void
    {
        $this->setPermission("op");
        $this->registerSubCommand(new GiveBookCommand("인첸트북", "인첸트북을 지급해요."));
        $this->registerSubCommand(new GiveCouponCommand("주문서", "주문서를 지급해요."));
    }

}