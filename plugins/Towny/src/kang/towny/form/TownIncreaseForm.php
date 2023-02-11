<?php

declare(strict_types=1);

namespace kang\towny\form;

use jojoe77777\FormAPI\ModalForm;
use kang\ServerUtils\ServerUtils;
use kang\towny\setting\Setting;
use kang\towny\town\Town;
use onebone\economyapi\EconomyAPI;
use pocketmine\player\Player;

class TownIncreaseForm extends ModalForm{

    public function __construct(private Town $town)
    {
        parent::__construct(function(Player $player, $data){
            if(!isset($data))return;
            switch ($data){
                case true:
                    $town = $this->town;
                    $economy = EconomyAPI::getInstance();
                    $price = $town->getIncreasePrice();

                    if ( $economy->myMoney($player) < $price ){
                        $left = $price - $economy->myMoney($player);
                        ServerUtils::error($player, $left."원이 부족하여 확장할 수 없어요.");
                        break;
                    }

                    $beforeSize = $town->getSize()." X ".$town->getSize();
                    $beforeMaintenance = $town->getMaintenance();

                    $town->increase();

                    $afterSize = $town->getSize()." X ".$town->getSize();
                    $afterMaintenance = $town->getMaintenance();

                    $msg = "마을이 확장되어 아래의 내용과 같은 변화가 있어요.";
                    $msg .= "\n크기: ".$beforeSize." => ".$afterSize;
                    $msg .= "\n유지비: ".$beforeMaintenance." => ".$afterMaintenance;

                    $town->broadMsg($msg);

                    break;
                case false:
                    break;
            }
        });
        $this->setTitle("마을 확장");
        $this->setContent([
            " ",
            "§b§l! §r현재 크기는 ".$town->getSize()." X ".$town->getSize()." 이에요.",
            "§b§l! §r다음 크기는 ".$town->getSize() + Setting::INCREASE_SIZE." X ".$town->getSize() + Setting::INCREASE_SIZE." 이에요.",
            "§b§l! §r확장 비용은 ".$town->getIncreasePrice()."원이에요.",
            "§b§l! §r정말 확장하시겠어요?",
            " "
        ]);
        $this->setButton1("확장할게요.");
        $this->setButton2("취소할게요.");
    }

}