<?php

declare(strict_types=1);

namespace kang\wild\form;

use jojoe77777\FormAPI\ModalForm;
use kang\wild\Wild;
use pocketmine\player\Player;

class ResetWildForm extends ModalForm {

    public function __construct()
    {
        parent::__construct(function(Player $player, $data){
            if(!isset($data))return;
            if($data==true){
                Wild::getInstance()->resetWild(Wild::WILD_WORLD, true);
            }
        });

        $this->setTitle("야생 초기화");
        $this->setContent([
            " ",
            "§c§l! §r정말 야생 월드를 초기화할까요?",
            " "
        ]);
        $this->setButton1("초기화할게요.");
        $this->setButton2("취소할게요.");
    }

}