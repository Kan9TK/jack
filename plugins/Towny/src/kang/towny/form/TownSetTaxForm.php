<?php

declare(strict_types=1);

namespace kang\towny\form;

use jojoe77777\FormAPI\CustomForm;
use kang\ServerUtils\ServerUtils;
use kang\towny\town\Town;
use kang\towny\Towny;
use pocketmine\player\Player;
use pocketmine\Server;

class TownSetTaxForm extends CustomForm{

    public function __construct(private Town $town)
    {
        parent::__construct(function(Player $player, $data){
            if(!isset($data[1]))return;
            if(!is_numeric($data[1]))return;

            $tax = intval($data[1]);
            $town = $this->town;

            $town->setTax($tax);
            $town->broadMsg("마을의 세금이 ".$tax."원으로 설정되었어요.");

        });

        $this->setTitle("세금 설정");
        $this->addLabel([
            "",
            "§b§l! §r현재 마을의 세금은 ".$town->getTax()."원이에요.",
            ""
        ]);
        $this->addInput("세금","","".$town->getTax());
    }

}