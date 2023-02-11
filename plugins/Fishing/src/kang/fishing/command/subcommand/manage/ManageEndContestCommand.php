<?php

declare(strict_types=1);

namespace kang\fishing\command\subcommand\manage;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\BaseSubCommand;
use kang\fishing\Fishing;
use kang\fishing\FishingContestManager;
use kang\ServerUtils\ServerUtils;
use pocketmine\command\CommandSender;

class ManageEndContestCommand extends BaseSubCommand{

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $plugin = Fishing::getInstance();
        if ( $plugin->getContestManager()->isEnd() ){
            ServerUtils::error($sender, "대회가 이미 종료되어 있어요.");
            return;
        }
        $plugin->getContestManager()->setMode(FishingContestManager::CONTEST_END);
    }

    protected function prepare(): void
    {
        $this->setPermission("op");
        $this->registerArgument(0, new IntegerArgument("시간(분)", true));
    }

}