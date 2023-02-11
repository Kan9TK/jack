<?php

namespace kang\wild\task;

use pocketmine\scheduler\AsyncTask;
use skymin\asyncqueue\AsyncQueue;
use ZipArchive;

class ExtractWorldTask extends AsyncTask
{

    /**
     * @param string $serverPath
     * @param string $worldArchPath
     */
    public function __construct(private string $serverPath, private string $worldArchPath)
    {
    }


    /**
     * @return void
     */
    public function onRun(): void
    {
        $zip = new ZipArchive;
        $zip->open($this->worldArchPath);
        $zip->extractTo($this->serverPath . "worlds");
        $zip->close();
    }

    public function onCompletion(): void
    {
        AsyncQueue::callBack($this);
    }
}