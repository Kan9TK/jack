<?php

namespace kang\wild\task;

use czechpmdevs\multiworld\MultiWorld;
use czechpmdevs\multiworld\util\WorldUtils;
use pocketmine\scheduler\AsyncTask;
use function closedir;
use function is_dir;
use function opendir;
use function readdir;
use function rmdir;
use function unlink;

class DeleteWorldTask extends AsyncTask
{

    /**
     * @param WorldUtils $worldUtils
     */
    public function __construct(private WorldUtils $worldUtils) {}

    /**
     * @return void
     */
    public function onRun(): void
    {
        $this->worldUtils::removeWorld("wild");
    }

}