<?php

declare(strict_types=1);

namespace kang\fishing\task;

use kang\fishing\Fishing;
use kang\fishing\FishingContestManager;
use kang\ServerUtils\ServerUtils;
use pocketmine\scheduler\Task;

class ContestTask extends Task{

    public function __construct(protected Fishing $plugin){

    }

    public function onRun(): void
    {
        if ( $this->plugin->getContestManager()->isEnd() ){
            $this->getHandler()->cancel();
            return;
        }
        if ( $this->plugin->getContestManager()->getEndTime() !== FishingContestManager::CONTEST_NO_END_TIME ) {
            if (time() >= $this->plugin->getContestManager()->getEndTime()) {
                $this->plugin->getContestManager()->setMode(FishingContestManager::CONTEST_END);
                $this->getHandler()->cancel();
            }
        }
    }

    public function onCancel(): void
    {
        parent::onCancel();
    }

}