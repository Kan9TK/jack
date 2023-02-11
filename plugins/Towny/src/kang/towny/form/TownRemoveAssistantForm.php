<?php

declare(strict_types=1);

namespace kang\towny\form;

use jojoe77777\FormAPI\CustomForm;
use kang\towny\town\Town;
use pocketmine\player\Player;

class TownRemoveAssistantForm extends CustomForm{

    public function __construct(private Town $town)
    {
        parent::__construct(function(Player $player, $data){
            if(!isset($data[1]))return;

            $town = $this->town;
            $targetName = array_keys($town->getAssistant())[$data[1]];
            $town->removeAssistant($targetName);
            $town->broadMsg($targetName."님의 부마을장 임명이 해제되었어요.");

        });

        $this->setTitle("부마을장 임명 해제");
        $this->addLabel([
            " ",
            "! 임명을 해제할 주민을 선택해주세요.",
            " "
        ]);
        $this->addDropdown("부마을장 목록",array_keys($town->getAssistant()));
    }

}