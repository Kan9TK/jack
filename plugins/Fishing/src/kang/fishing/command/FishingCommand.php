<?php

declare(strict_types=1);

namespace kang\fishing\command;

use CortexPE\Commando\BaseCommand;
use kang\fishing\command\subcommand\FishingBoxCommand;
use kang\fishing\command\subcommand\FishingRankingCommand;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;

class FishingCommand extends BaseCommand{

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        // TODO: Implement onRun() method.
    }

    protected function prepare(): void
    {
        $this->setPermission("true");
        $this->registerSubCommand(new FishingBoxCommand("보관함", "낚시 보관함을 확인해요."));
        $this->registerSubCommand(new FishingRankingCommand("순위", "낚시 대회 순위를 확인해요."));
    }

}