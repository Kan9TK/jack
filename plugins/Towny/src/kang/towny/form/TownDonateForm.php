<?php

declare(strict_types=1);

namespace kang\towny\form;

use jojoe77777\FormAPI\CustomForm;
use kang\ServerUtils\ServerUtils;
use kang\towny\Towny;
use onebone\economyapi\EconomyAPI;
use pocketmine\player\Player;

class TownDonateForm extends CustomForm{

    public function __construct(Player $player)
    {

        parent::__construct(function(Player $player, $data){
            if(!isset($data[1]))return;
            if(!is_numeric($data[1]))return;

            $price = intval($data[1]);
            $economy = EconomyAPI::getInstance();
            if ( $economy->myMoney($player) < $price ){
                $left = $price - $economy->myMoney($player);
                ServerUtils::error($player, $left."원이 부족하여 기부할 수 없어요.");
                return;
            }

            $plugin = Towny::getInstance();
            $town = $plugin->getTownManager()->getTownByName($player->getName());
            $town->donate($player->getName(), $price);
            ServerUtils::msg($player, "성공적으로 마을에 ".$price."원을 기부했어요!");

        });

        $plugin = Towny::getInstance();
        $economy = EconomyAPI::getInstance();
        $town = $plugin->getTownManager()->getTownByName($player->getName());

        $this->setTitle("마을 기부");
        $this->addLabel([
            "",
            "§b§l! §r".$player->getName()."님의 보유 금액은 ".$economy->myMoney($player)."원이에요.",
            "§b§l! §r마을 금고의 잔액은 ".$town->getMoney()."원이에요.",
            "§b§l! §r".$player->getName()."님의 총 기부 금액은 ".$town->getDonation($player->getName())."원이에요.",
            ""
        ]);
        $this->addInput("기부할 금액");
    }

}