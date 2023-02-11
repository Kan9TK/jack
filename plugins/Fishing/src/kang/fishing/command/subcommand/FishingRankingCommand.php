<?php

declare(strict_types=1);

namespace kang\fishing\command\subcommand;

use CortexPE\Commando\BaseSubCommand;
use kang\fishing\Fishing;
use kang\fishing\FishingContestManager;
use kang\fishing\inventory\FishInventory;
use kang\ServerUtils\ServerUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class FishingRankingCommand extends BaseSubCommand{

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!$sender instanceof Player)return;
        switch ( Fishing::getInstance()->getContestManager()->getMode() ) {
            case FishingContestManager::CONTEST_START:

                ServerUtils::sendRanking(
                    $sender,
                    Fishing::getInstance()->getContestManager()->getRankings(),
                    "낚시 대회",
                    1,
                    5,
                    "cm"
                ); // ARSORT ARRAY

                break;
            case FishingContestManager::CONTEST_END:
                ServerUtils::error($sender, "현재 낚시 대회가 진행 중이 아니에요.");
                break;
        }
    }

    protected function prepare(): void
    {
        $this->setPermission("true");
    }

}