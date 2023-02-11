<?php

declare(strict_types=1);

namespace kang\towny\form;

use jojoe77777\FormAPI\CustomForm;
use kang\ServerUtils\ServerUtils;
use kang\towny\town\Town;
use kang\towny\Towny;
use pocketmine\player\Player;
use pocketmine\Server;

class TownInviteForm extends CustomForm{
    
    public function __construct(private Town $town)
    {
        parent::__construct(function(Player $player, $data){
            if(!isset($data[1]))return;

            $targetName = $data[1];
            $server = Server::getInstance();

            if ( ($target = $server->getPlayerExact($targetName)) == null ){
                ServerUtils::error($player, "해당 닉네임의 유저가 서버에 접속 중이 아니에요.");
                return;
            }

            $targetName = $target->getName();

            if ( $targetName == $player->getName() ){
                ServerUtils::error($player, $targetName."자기 자신을 초대할 수 없어요.");
                return;
            }

            $plugin = Towny::getInstance();
            $townManager = $plugin->getTownManager();
            if ( $townManager->getTownByName($targetName) !== null ){
                ServerUtils::error($player, $targetName."님은 이미 마을에 소속되어 있어요.");
                return;
            }

            $town = $this->town;

            $sessionManager = $plugin->getSessionManager();
            $sessionManager->sendInvitation($town, $target);
            ServerUtils::msg($target, $town->getName()." 마을로부터 초대되었어요. 수락하려면 /마을 초대수락 (을)를 입력해주세요.");
            ServerUtils::msg($player, "성공적으로 ".$targetName."님을 초대했어요.");

            
        });
        $this->setTitle("주민 초대");
        $this->addLabel([
            " ",
            "§b§l! §r초대할 유저의 닉네임을 입력해주세요",
            " "
        ]);
        $this->addInput("닉네임");
    }

}