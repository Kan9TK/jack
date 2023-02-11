<?php

declare(strict_types=1);

namespace kang\wild\form;

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;

class ChooseWildForm extends SimpleForm {
    
    public function __construct()
    {
        parent::__construct(function(Player $player, $data){
            if(!isset($data))return;
            if($data==0){
                $player->sendForm(new TeleportNormalWildForm);
            }
            elseif($data==1){
                $player->sendForm(new TeleportTownWildForm);
            }
        });

        $this->setTitle("야생");
        $this->setContent([
            " ",
            "§b§l! §r원하는 야생을 선택해주세요!",
            " "
        ]);
        $this->addButton("일반 야생\n일반 야생으로 이동할게요!");
        $this->addButton("마을 야생\n마을 야생으로 이동할게요!");
    }

}