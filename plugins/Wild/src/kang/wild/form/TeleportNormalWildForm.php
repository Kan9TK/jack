<?php

declare(strict_types=1);

namespace kang\wild\form;

use jojoe77777\FormAPI\SimpleForm;
use kang\wild\Wild;
use pocketmine\form\Form;
use pocketmine\player\Player;

class TeleportNormalWildForm extends SimpleForm {

    public function __construct()
    {
        parent::__construct(function(Player $player, $data){
            if(!isset($data))return;
            if($data==0){
                Wild::getInstance()->RandomTeleport($player, Wild::WILD_WORLD, "일반 야생");
            }
        });

        $this->setTitle("일반 야생");
        $this->setContent([
            " ",
            "§b§l! §r자유롭게 생활할 수 있는 야생이에요.",
            "",
            "§c§l! §r입장 전에 법전을 확인해주세요.",
            "§c§l! §r유저간의 PVP가 가능해요.",
            "§c§l! §r사망 시 인벤토리는 보호되지만 소지금의 5P를 잃어요.",
            "§c§l! §r야생의 크기 제한은 20000X20000 이에요.",
            "§c§l! §r야생은 주기적으로 초기화 돼요.",
            " "
        ]);
        $this->addButton("확인했어요!\n일반 야생으로 이동할게요!");

    }

}