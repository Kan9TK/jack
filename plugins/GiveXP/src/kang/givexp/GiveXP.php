<?php

declare(strict_types=1);

namespace kang\givexp;

use kang\givexp\command\XPCommand;
use pocketmine\plugin\PluginBase;

class GiveXP extends PluginBase{

    protected function onEnable(): void
    {
        $this->getServer()->getCommandMap()->registerAll($this->getName(), [
            new XPCommand($this, "xp", "바닐라 경험치를 지급해요.")
        ]);
    }

}