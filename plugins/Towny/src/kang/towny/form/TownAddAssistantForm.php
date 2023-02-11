<?php

declare(strict_types=1);

namespace kang\towny\form;

use jojoe77777\FormAPI\CustomForm;
use kang\towny\town\Town;
use pocketmine\player\Player;

class TownAddAssistantForm extends CustomForm{

    public function __construct(private Town $town)
    {
        parent::__construct(function(Player $player, $data){
            if(!isset($data[1]))return;

            $town = $this->town;
            $targetName = $town->getNormalMembers()[$data[1]];
            $town->addAssistant($targetName);
            $town->broadMsg($targetName."님이 부마을장으로 임명되었어요.");

        });

        $this->setTitle("부마을장 임명");
        $this->addLabel([
            " ",
            "§b§l! §r임명할 주민을 선택해주세요.",
            " ",
            "§c§l! §r부마을장은 초대,추방,확장,출금이 가능해요.",
            " "
        ]);
        $this->addDropdown("주민 목록",$town->getNormalMembers());
    }

}