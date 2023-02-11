<?php

declare(strict_types=1);

namespace kang\towny\form;

use jojoe77777\FormAPI\SimpleForm;
use kang\ServerUtils\ServerUtils;
use kang\towny\setting\Setting;
use pocketmine\player\Player;

class TownNoHaveForm extends SimpleForm{

    public function __construct()
    {
        parent::__construct(function(Player $player, $data){
            if(!isset($data))return;
            switch ($data){
                case 0:
                    if ( $player->getWorld()->getFolderName() !== Setting::WORLD_NAME ){
                        ServerUtils::error($player, "마을 야생에서만 생성할 수 있어요. /야생 명령어를 통해 이동할 수 있어요.");
                        break;
                    }
                    $player->sendForm(new TownCreateForm());
                    break;
                case 1:
                    $player->sendForm(new TownBoardListForm());
                    break;
            }
        });
        $this->setTitle("마을");
        $this->setContent([
            "",
            "§b§l! §r원하는 기능을 선택해주세요.",
            " "
        ]);
        $this->addButton("마을 생성\n마을을 생성해요.");
        $this->addButton("모집 게시판\n마을원 모집 게시판을 확인해요.");
    }

}