<?php

declare(strict_types=1);

namespace kang\towny\form;

use jojoe77777\FormAPI\SimpleForm;
use kang\ServerUtils\ServerUtils;
use kang\towny\town\Town;
use kang\towny\setting\Setting;
use pocketmine\player\Player;

class TownDonationListForm extends SimpleForm{

    public function __construct(private Town $town)
    {
        parent::__construct(function(Player $player, $data){
            if(!isset($data))return;
        });
        $this->setTitle("마을 기부 현황");

        $list = "";
        foreach ( $town->getAllDonation() as $playerName => $price ){
            $list .= $playerName." => ".$price."원\n";
        }

        $this->setContent([
            " ",
            "마을의 기부 금액은 총 ".$town->getAllDonationPrice()."원이에요.",
            "\n<==== 기부 현황 ====>",
            $list,
            " "
        ]);
    }

}