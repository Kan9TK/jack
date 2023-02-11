<?php

declare(strict_types=1);

namespace alvin0319\Area;

use alvin0319\Area\area\AreaManager;
use alvin0319\Area\command\area\AreaCommand;
use alvin0319\Area\command\world\WorldManageCommand;
use alvin0319\Area\listener\EventListener;
use alvin0319\Area\world\WorldManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class AreaLoader extends PluginBase{
    use SingletonTrait;

    protected WorldManager $worldManager;
    protected AreaManager $areaManager;

    public static function getInstance() : AreaLoader{
        return self::$instance;
    }

    public function onLoad() : void{
        self::$instance = $this;
        $this->worldManager = new WorldManager($this);
        $this->areaManager = new AreaManager($this);
    }

    public function onEnable(): void{

        if(!is_dir($dir = $this->getDataFolder() . "area")){
            mkdir($dir);
        }

        $this->getServer()->getCommandMap()->registerAll(strtolower($this->getName()), [
            new WorldManageCommand($this, "월드관리", "월드 관리 명령어예요."),
            new AreaCommand($this, "땅", "땅 명령어예요.")
        ]);
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    }

    public function onDisable(): void{
        $this->worldManager->save();
        $this->areaManager->save();
    }

    public function getWorldManager() : WorldManager{
        return $this->worldManager;
    }

    public function getAreaManager() : AreaManager{
        return $this->areaManager;
    }

}