<?php

namespace kang\ServerUtils\task;

use pocketmine\scheduler\AsyncTask;
use skymin\asyncqueue\AsyncQueue;

class RankingTask extends AsyncTask {

    public int $playerRank = 0;

    public function __construct(private array $data, private int $offset, private ?int $length, private string $playerName) {
    }

    public function onRun() : void{

        $data = (array)$this->data;
        $playerName = (string)$this->playerName;

        arsort($data);

        $i = 0;
        foreach($data as $key => $value){
            $i++;
            if($playerName==$key){
                $this->playerRank = $i;
                break;
            }
        }
        $data = array_slice($data, $this->offset, $this->length, true);
        $this->setResult($data);
    }

    public function onCompletion() : void{
        AsyncQueue::callBack($this);
    }
}