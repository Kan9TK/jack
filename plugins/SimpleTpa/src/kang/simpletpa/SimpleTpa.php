<?php

declare(strict_types=1);

namespace kang\simpletpa;

use kang\ServerUtils\ServerUtils;
use kang\simpletpa\command\TeleportAcceptCommand;
use kang\simpletpa\command\TeleportCommand;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

class SimpleTpa extends PluginBase{

    protected static SimpleTpa $instance;

    protected array $requestList = [];

    public const TELEPORT_TIME = 10;

    public static function getInstance() : SimpleTpa{
        return self::$instance;
    }

    public function onLoad(): void
    {
        self::$instance = $this;
    }

    public function onEnable(): void{
        $this->getServer()->getCommandMap()->registerAll($this->getName(),[
            new TeleportCommand($this, "티피", "해당 플레이어에게 티피를 요청해요."),
            new TeleportAcceptCommand($this, "티피수락", "티피 수락 명령어예요.")
        ]);
    }

    public function onDisable(): void{

    }

    public function requestTeleport(Player $sender, Player $target) : void{
        $this->requestList[$target->getName()] = ["sender"=>$sender->getName(),"time"=>time() + self::TELEPORT_TIME];
    }

    public function acceptTeleport(Player $target) : bool{
        if ( ! isset ( $this->requestList[$target->getName()] ) ) return false;

        $request = $this->requestList[$target->getName()];

        $senderName = $request["sender"];
        if ( ($sender = $this->getServer()->getPlayerExact($senderName)) == null ){
            ServerUtils::error($target, $senderName."님이 서버에 접속 중이지 않아요.");
            unset($this->requestList[$target->getName()]);
            return true;
        }

        if ( time() > $request["time"] ){
            ServerUtils::error($target, "티피 요청이 시간 초과로 인해 만료되었어요.");
            unset($this->requestList[$target->getName()]);
            return true;
        }

        $target->teleport($sender->getPosition());
        ServerUtils::addTeleportSound($target);
        return true;

    }

}