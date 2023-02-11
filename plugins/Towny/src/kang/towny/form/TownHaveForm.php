<?php

declare(strict_types=1);

namespace kang\towny\form;

use jojoe77777\FormAPI\SimpleForm;
use kang\ServerUtils\ServerUtils;
use kang\towny\setting\Setting;
use kang\towny\Towny;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;

class TownHaveForm extends SimpleForm{

    public function __construct()
    {
        parent::__construct(function(Player $player, $data){
            if(!isset($data))return;

            $plugin = Towny::getInstance();
            $town = $plugin->getTownManager()->getTownByName($player->getName());

            switch ($data){
                case 0:
                    $player->sendForm(new TownInfoForm($town));
                    break;
                case 1:
                    $town->teleport($player);
                    Towny::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use($player) : void{
                        ServerUtils::addTeleportSound($player);
                    }), 5);
                    break;
                case 2:
                    $player->sendForm(new TownDonateForm($player));
                    break;
                case 3:
                    $player->sendForm(new TownDonationListForm($town));
                    break;
            }
        });

        $taxDate = date("Y")."-".date("m")."-".date("d")." ".Setting::TAX_TIME.":00:00";
        $diff = max(time(),strtotime($taxDate)) - min(time(),strtotime($taxDate));

        $this->setTitle("마을");
        $this->setContent([
            " ",
            "§b§l! §r유지비 & 세금 납부까지 ".ServerUtils::TimeToString($diff)." 남았어요.",
            " "
        ]);
        $this->addButton("정보\n마을 정보를 확인해요.");
        $this->addButton("스폰\n마을 스폰으로 이동해요.");
        $this->addButton("기부\n마을에 기부해요.");
        $this->addButton("기부 현황\n마을 기부 현황을 확인해요.");
    }

}