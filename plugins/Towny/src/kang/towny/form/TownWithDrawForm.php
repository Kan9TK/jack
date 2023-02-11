<?php

declare(strict_types=1);

namespace kang\towny\form;

use jojoe77777\FormAPI\CustomForm;
use kang\ServerUtils\ServerUtils;
use kang\towny\town\Town;
use kang\towny\Towny;
use onebone\economyapi\EconomyAPI;
use pocketmine\player\Player;

class TownWithDrawForm extends CustomForm{

    public function __construct(protected Town $town)
    {

        parent::__construct(function(Player $player, $data){
            if(!isset($data[1]))return;
            if(!is_numeric($data[1]))return;

            $price = intval($data[1]);
            if ( $this->town->getMoney() < $price ){
                ServerUtils::error($player, "마을 잔고가 부족해요.");
                return;
            }

            $this->town->reduceMoney($price);
            ServerUtils::msg($player, "성공적으로 {$price}원을 출금했어요.");

        });

        $this->setTitle("마을 출금");
        $this->addLabel([
            "",
            "§b§l! §r마을 금고의 잔액은 ".$town->getMoney()."원이에요.",
            ""
        ]);
        $this->addInput("출금할 금액");
    }

}