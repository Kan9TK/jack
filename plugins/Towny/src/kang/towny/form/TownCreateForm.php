<?php

declare(strict_types=1);

namespace kang\towny\form;

use jojoe77777\FormAPI\CustomForm;
use kang\ServerUtils\ServerUtils;
use kang\towny\setting\Setting;
use kang\towny\town\TownFactory;
use kang\towny\Towny;
use onebone\economyapi\EconomyAPI;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class TownCreateForm extends CustomForm {

    public function __construct()
    {
        parent::__construct(function(Player $player, $data){

            if(!isset($data[1]))return;

            $plugin = Towny::getInstance();
            
            $townManager = $plugin->getTownManager();
            if ( $townManager->getTownByName($player->getName()) !== null ){
                ServerUtils::error($player, "이미 마을에 소속되어 있어요.");
                return;
            }

            if ( ($whoTown = $townManager->getTownByPos($player->getPosition())) !== null ) {
                ServerUtils::error($player, "해당 위치에 마을이 존재해요. 한적한 땅을 찾아 생성해보세요.");
                return;
            }

            $economy = EconomyAPI::getInstance();
            $createPrice = Setting::CREAT_PRICE;
            if ( $economy->myMoney($player) < $createPrice ){
                $left = $createPrice - $economy->myMoney($player);
                ServerUtils::error($player, $left."원이 부족하여 생성할 수 없어요.");
                return;
            }

            $townName = TextFormat::clean($data[1]);

            if(mb_strlen($townName, "utf-8") > 8){
                ServerUtils::error($player, "마을 이름이 8 글자를 초과할 수 없어요.");
                return;
            }

            $economy->reduceMoney($player, $createPrice);

            $town = TownFactory::createTown($player, $townName);
            $townManager->loadTown($town);

            $player->sendTitle($townName,"축하해요! 마을을 생성했어요!");
            ServerUtils::msg($player, "성공적으로 마을을 생성했어요. /마을 명령어를 통해 마을을 관리해보세요!");
            ServerUtils::broad($player->getName()."님이 ".$townName."마을을 생성했어요!");

        });

        $this->setTitle("마을 생성");
        $this->addLabel([
            "§b§l! §r마을 생성 비용은 ".Setting::CREAT_PRICE."원이에요.",
            "",
            "§c§l! §r8 글자를 초과할 수 없어요.",
            "§c§l! §r근처에 다른 마을이 없는 지 확인해주세요."
        ]);
        $this->addInput("마을 이름");
    }



}