<?php

declare(strict_types=1);

namespace kang\itemeditor;

use kang\itemeditor\command\ItemEditorCommand;
use pocketmine\plugin\PluginBase;

class ItemEditor extends PluginBase{
    
    public function onEnable() : void{
        $this->getServer()->getCommandMap()->registerAll($this->getName(), [
            new ItemEditorCommand($this, "itemeditor", "edit items", ["ie","아이템수정"])
        ]);
    }
    
}