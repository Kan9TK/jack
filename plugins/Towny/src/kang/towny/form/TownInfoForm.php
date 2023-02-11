<?php

declare(strict_types=1);

namespace kang\towny\form;

use jojoe77777\FormAPI\SimpleForm;
use kang\ServerUtils\ServerUtils;
use kang\towny\town\Town;
use kang\towny\setting\Setting;
use pocketmine\player\Player;

class TownInfoForm extends SimpleForm{

    public function __construct(private Town $town)
    {
        parent::__construct(function(Player $player, $data){
            if(!isset($data))return;
        });
        $this->setTitle("마을 정보");
        $this->setContent([
            "",
            "§b§l▶ §r".$town->getName()." 마을 §b§l◀",
            "\n§b§l• §r인원: ".count($town->getMembers())."명",
            "§b§l• §r마을장: ".$town->getOwner(),
            "§b§l• §r부마을장: ".implode(", ",array_keys($town->getAssistant())),
            "§b§l• §r주민: ".implode(", ",$town->getNormalMembers()),
            "\n§b§l• §r금고: ".$town->getMoney()."원",
            "§b§l• §r유지비: ".$town->getMaintenance()."원",
            "§b§l• §r세금: ".$town->getTax()."원",
            ""
        ]);
    }

}