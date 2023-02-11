<?php

declare(strict_types=1);

namespace kang\towny\form;

use jojoe77777\FormAPI\ModalForm;
use kang\ServerUtils\ServerUtils;
use kang\towny\town\Town;
use kang\towny\town\TownFactory;
use pocketmine\player\Player;

class TownDeleteForm extends ModalForm{

    public function __construct(private Town $town)
    {
        parent::__construct(function(Player $player, $data){
            if(!isset($data))return;
            switch ($data){
                case true:
                    TownFactory::deleteTown($player->getName());
                    ServerUtils::msg($player, "성공적으로 마을이 해체되었어요.");
                    break;
                case false:
                    break;
            }
        });
        $this->setTitle("마을 해체");
        $this->setContent([
            " ",
            "§c§l! §r해체하면 되돌릴 수 없어요.",
            "§c§l! §r정말 해체하시겠어요?",
            " "
        ]);
        $this->setButton1("해체할게요.");
        $this->setButton2("취소할게요.");
    }

}