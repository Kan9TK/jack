<?php

/**
 * @name LoadAllWorlds
 * @author kang
 * @version 1.0.0
 * @api 4.0.0
 * @main LoadAllWorlds\LoadAllWorlds
 */

namespace LoadAllWorlds;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\block\LeavesDecayEvent;

class LoadAllWorlds extends PluginBase implements Listener
{
    public function onEnable() : void{
        foreach (array_diff(scandir($this->getServer()->getDataPath() . "worlds"), ["..", "."]) as $levelName) {
            $this->getServer()->getWorldManager()->loadWorld($levelName, true);
        }
    }

}