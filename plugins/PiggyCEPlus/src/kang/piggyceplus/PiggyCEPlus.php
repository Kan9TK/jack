<?php

declare(strict_types=1);

namespace kang\piggyceplus;

use kang\piggyceplus\command\CustomEnchantCommand;
use kang\piggyceplus\command\EnchantListCommand;
use kang\piggyceplus\command\GiveBookCommand;
use kang\piggyceplus\command\ManageCECommand;
use kang\piggyceplus\listener\EventListener;
use pocketmine\plugin\PluginBase;

class PiggyCEPlus extends PluginBase{
    
    public static PiggyCEPlus $instance;
    
    public static function getInstance() : PiggyCEPlus{
        return self::$instance;
    }
    
    protected function onLoad(): void
    {
        self::$instance = $this;
    }

    protected function onEnable(): void
    {
        $this->getServer()->getCommandMap()->registerAll($this->getName(),[
            new CustomEnchantCommand($this, "특수인첸트", "특수 인첸트 명령어예요."),
            new EnchantListCommand($this, "특수인첸트목록", "특수 인첸트 목록을 확인해요."),
            new ManageCECommand($this, "특수인첸트관리", "특수 인첸트 관리 명령어예요."),
        ]);
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
    }

}