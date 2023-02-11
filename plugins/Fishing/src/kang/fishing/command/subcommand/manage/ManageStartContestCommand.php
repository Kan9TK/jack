<?php

declare(strict_types=1);

namespace kang\fishing\command\subcommand\manage;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\BaseSubCommand;
use kang\fishing\Fishing;
use kang\fishing\FishingContestManager;
use kang\ServerUtils\ServerUtils;
use pocketmine\command\CommandSender;

class ManageStartContestCommand extends BaseSubCommand{

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $plugin = Fishing::getInstance();
        if ( $plugin->getContestManager()->isStart() ){
            ServerUtils::error($sender, "대회가 이미 시작되었어요.");
            return;
        }
        if(isset($args["시간(분)"])){
            $time = intval($args["시간(분)"]) * 60;
            $plugin->getContestManager()->setEndTime(time()+$time);
            ServerUtils::broad("낚시 대회가 시작되었어요! 종료까지 ".(ServerUtils::TimeToString($time, true, false))." 남았어요.");
        }else{
            $plugin->getContestManager()->setEndTime(FishingContestManager::CONTEST_NO_END_TIME);
            ServerUtils::broad("낚시 대회가 시작되었어요!");
        }

        $contestManager = $plugin->getContestManager();

        $contestManager->setMode(FishingContestManager::CONTEST_START);

        $task = $contestManager->getTask();
        if ( $contestManager->getTask()->getHandler() == null ){
            $plugin->getScheduler()->scheduleRepeatingTask($task, 20*60);
        }

    }

    protected function prepare(): void
    {
        $this->setPermission("op");
        $this->registerArgument(0, new IntegerArgument("시간(분)", true));
    }

}