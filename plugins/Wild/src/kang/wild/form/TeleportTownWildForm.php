<?php

declare(strict_types=1);

namespace kang\wild\form;

use jojoe77777\FormAPI\SimpleForm;
use kang\wild\Wild;
use pocketmine\form\Form;
use pocketmine\player\Player;

class TeleportTownWildForm extends SimpleForm {

    public function __construct()
    {
        parent::__construct(function(Player $player, $data){
            if(!isset($data))return;
            if($data==0){
                Wild::getInstance()->RandomTeleport($player, Wild::TOWN_WORLD, "마을 야생");
            }
        });

        $this->setTitle("마을 야생");
        $this->setContent([
            " ",
            "§b§l! §r마을을 만들 수 있는 야생이에요.",
            "§b§l! §r/마을 명령어를 이용할 수 있어요.",
            "",
            "§c§l! §r자신의 마을이 아닌 블럭을 건드릴 수 없어요.",
            "§c§l! §r유저간의 PVP가 불가능해요.",
            "§c§l! §r사망 시 아이템이나 돈을 잃지 않아요.",
            "§c§l! §r동물이 스폰되지 않아요.",
            " "
        ]);
        $this->addButton("확인했어요!\n마을 야생으로 이동할게요!");

    }

}