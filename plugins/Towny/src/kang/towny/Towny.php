<?php

declare(strict_types=1);

namespace kang\towny;

use kang\towny\board\BoardManager;
use kang\towny\command\TownAcceptCommand;
use kang\towny\command\TownBoardCommand;
use kang\towny\command\TownCommand;
use kang\towny\command\TownManageCommand;
use kang\towny\listener\EventListener;
use kang\towny\session\SessionManager;
use kang\towny\setting\Setting;
use kang\towny\task\TownTaxTask;
use kang\towny\town\TownManager;
use pocketmine\plugin\PluginBase;

class Towny extends PluginBase{

    private static Towny $instance;
    private Setting $setting;
    private TownManager $townManager;
    private BoardManager $boardManager;
    private SessionManager $sessionManager;

    public static function getInstance() : Towny{
        return self::$instance;
    }

    public function onLoad() : void{
        self::$instance = $this;
    }

    public function onEnable() : void{
        $this->setting = new Setting($this);
        $this->townManager = new TownManager($this);
        $this->boardManager = new BoardManager($this);
        $this->sessionManager = new SessionManager($this);
        $this->getServer()->getCommandMap()->registerAll(strtolower($this->getName()),[
            new TownCommand($this, "마을", "마을 명령어예요.")
        ]);
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getScheduler()->scheduleRepeatingTask(new TownTaxTask($this), 20*60);
    }

    public function onDisable(): void{
        $this->townManager->save();
        $this->boardManager->save();
    }

    public function getSetting() : Setting{
        return $this->setting;
    }

    public function getTownManager() : TownManager{
        return $this->townManager;
    }

    public function getBoardManager() : BoardManager{
        return $this->boardManager;
    }

    public function getSessionManager() : SessionManager{
        return $this->sessionManager;
    }

}