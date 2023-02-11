<?php

declare(strict_types=1);

namespace kang\fishing\command;

use CortexPE\Commando\BaseCommand;
use kang\fishing\command\subcommand\manage\ManageEndContestCommand;
use kang\fishing\command\subcommand\manage\ManageRewardCommand;
use kang\fishing\command\subcommand\manage\ManageStartContestCommand;
use pocketmine\command\CommandSender;

class ManageFishingCommand extends BaseCommand{

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        // TODO: Implement onRun() method.
    }

    protected function prepare(): void
    {
        $this->setPermission("op");
        $this->registerSubCommand(new ManageStartContestCommand("대회시작", "대회를 시작해요."));
        $this->registerSubCommand(new ManageEndContestCommand("대회종료", "대회를 종료하고 보상을 지급해요."));
        $this->registerSubCommand(new ManageRewardCommand("보상", "대회 보상을 확인해요."));
    }

}