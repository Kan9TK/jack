<?php

namespace kang\ServerUtils\task;

use pocketmine\scheduler\AsyncTask;
use skymin\asyncqueue\AsyncQueue;

class ArsortTask extends AsyncTask {

    public function __construct(private array $data) {
    }

    public function onRun() : void{

        $data = (array)$this->data;
        arsort($data);
        $this->setResult($data);
    }

    public function onCompletion() : void{
        AsyncQueue::callBack($this);
    }

}