<?php

declare(strict_types=1);

namespace kang\fishing;

use kang\dataconfig\DataConfig;
use kang\fishing\fish\Fish;
use kang\fishing\task\ContestTask;
use kang\mailbox\MailBox;
use kang\ServerUtils\ServerUtils;
use kang\ServerUtils\task\ArsortTask;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\Task;
use skymin\asyncqueue\AsyncQueue;

class FishingContestManager{

    protected DataConfig $config;

    public const CONTEST_START = "start";
    public const CONTEST_END = "end";
    public const CONTEST_NO_END_TIME = "";

    public function __construct(protected Fishing $plugin){
        $this->config = new DataConfig($plugin->getDataFolder()."config.json",DataConfig::TYPE_JSON,[
            "contest"=>self::CONTEST_END,
            "contestEndTime"=>"",
            "ranking"=>[],
            "reward"=>[]
        ]);
    }

    public function isStart() : bool{
        return $this->config->get("contest", self::CONTEST_START) === self::CONTEST_START;
    }

    public function isEnd() : bool{
        return $this->config->get("contest", self::CONTEST_END) === self::CONTEST_END;
    }

    public function setMode(string $mode){
        $this->config->set("contest", $mode);
        switch ( $mode ){
            case self::CONTEST_START:
                break;
            case self::CONTEST_END:
                ServerUtils::broad("낚시 대회가 종료되었어요. 1위 ~ 5위에게는 우편함으로 보상이 지급되었어요.");
                $task = ServerUtils::getArsortTask($this->getRankings());
                $callable = function (ArsortTask $task) : void{
                    $c = 1;
                    foreach ( $task->getResult() as $playerName => $score ){
                        foreach ( $this->getRewards() as $index => $reward ){
                            $item = $this->getReward($index);
                            MailBox::addMail($playerName, $item, "낚시 대회 보상");
                        }
                        $c++;
                        if($c>5){
                            break;
                        }
                    }
                    $this->config->set("ranking", []); // 순위 초기화 1
                    Fishing::getInstance()->getSessionManager()->resetRanking(); // 순위 초기화 2
                };
                AsyncQueue::submit($task, $callable);

                break;
        }
    }

    public function getMode() : string{
        return $this->config->get("contest", self::CONTEST_END);
    }

    public function setEndTime(string|int $time){
        $this->config->set("contestEndTime", $time);
    }

    public function getEndTime() : string|int{
        return $this->config->get("contestEndTime", self::CONTEST_NO_END_TIME);
    }

    public function getReward(int $index) : ?Item{
        $rewards = $this->getRewards();
        return $rewards[$index] === null ? null : Item::jsonDeserialize($rewards[$index]);
    }

    public function getRewards() : array{
        return $this->config->get("reward", []);
    }

    public function setRewards(array $rewards){
        $this->config->set("reward", $rewards);
    }

    public function checkRanking(Player $player, Fish $fish) : bool{
        if(!isset($this->getRankings()[$player->getName()])){
            $this->config->set("ranking", [$player->getName()=>$fish->getLength()]);
        }else{
            if ( $fish->getLength() > $this->getRankingScore($player) ){
                $this->config->set("ranking", [$player->getName()=>$fish->getLength()]);
            }else{
                return false;
            }
        }
        return true;
    }

    public function getRankingScore(Player $player) : int{
        return $this->config->get("ranking")[$player->getName()] ?? 0;
    }

    public function sendRanking(Player $player) : void{
        $task = ServerUtils::getArsortTask($this->getRankings());
        $callable = function (ArsortTask $task) use ($player): void {
            $c = 0;
            foreach ($task->getResult() as $playerName => $score) {
                $c++;
                if ($c >= 1 && $c <= 5) { // 1위 ~ 5위만 체크
                    if ($player->getName() === $playerName) {
                        $ranking = Fishing::getInstance()->getSessionManager()->getRanking($player); // 기본 999위
                        if ($c < $ranking) { // 이전 기록보다 좋을 경우 | 순위가 더 높을 때 (1 < 999)
                            ServerUtils::broad($player->getName() . "님이 낚시 대회에서 " . $c . "위를 차지했어요!");
                            Fishing::getInstance()->getSessionManager()->setRanking($player, $c);
                        }
                    }
                }
            }
        };
        AsyncQueue::submit($task, $callable);
    }

    public function getRankings() : array{
        return $this->config->get("ranking", []);
    }

    public function getTask() : Task{
        return new ContestTask(Fishing::getInstance());
    }

    public function save() : void{
        $this->config->save($this->config->getAll());
    }

}