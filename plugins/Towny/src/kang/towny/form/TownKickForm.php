<?php

declare(strict_types=1);

namespace kang\towny\form;

use jojoe77777\FormAPI\CustomForm;
use kang\towny\town\Town;
use pocketmine\player\Player;

class TownKickForm extends CustomForm{

    public function __construct(private Town $town)
    {
        parent::__construct(function(Player $player, $data){
            if(!isset($data[1]))return;

            $town = $this->town;
            $targetName = $town->getNormalMembers()[$data[1]];
            $town->kick($targetName);
            $town->broadMsg($targetName."님이 마을로부터 추방되었어요. 처리자: ".$player->getName());

        });

        $this->setTitle("마을 추방");
        $this->addLabel([
            " ",
            "! 추방할 주민을 선택해주세요.",
            " "
        ]);
        $this->addDropdown("주민 목록",$town->getNormalMembers());
    }

}