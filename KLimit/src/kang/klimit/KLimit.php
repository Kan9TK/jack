<?php

declare(strict_types=1);
	
namespace kang\klimit;

use pocketmine\plugin\PluginBase;

class KLimit extends PluginBase {
	
	public function onEnable(): void{
		$this->getServer()->getCommandMap()->registerAll($this->getName(), [
            new MainCommand($this)
        ]);
		$this->a = new Config($this->getDataFolder() . 'Data.yml', Config::YAML);
        $this->data = $this->a->getAll();
    }

    public function onDisable()
    {
        $this->a->setAll($this->data);
        $this->a->save();
    }
	
}