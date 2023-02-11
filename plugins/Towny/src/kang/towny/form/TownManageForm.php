<?php

declare(strict_types=1);

namespace kang\towny\form;

use jojoe77777\FormAPI\SimpleForm;
use kang\ServerUtils\ServerUtils;
use kang\towny\town\Town;
use kang\towny\Towny;
use pocketmine\player\Player;

class TownManageForm extends SimpleForm{

    public function __construct(private Town $town)
    {
        parent::__construct(function(Player $player, $data){
            if(!isset($data))return;

            $plugin = Towny::getInstance();
            $town = $this->town;

            switch ($data){
                case 0:
                    $player->sendForm(new TownInviteForm($town));
                    break;
                case 1:
                    $player->sendForm(new TownKickForm($town));
                    break;
                case 3:
                    $player->sendForm(new TownWithDrawForm($town));
                    break;
                case 3:
                    $player->sendForm(new TownIncreaseForm($town));
                    break;
                case 4:
                    if ( ! $town->isOwner($player->getName()) ){
                        ServerUtils::error($player, "마을장만 사용할 수 있는 기능이에요.");
                        break;
                    }
                    $player->sendForm(new TownSetTaxForm($town));
                    break;
                case 5:
                    if ( ! $town->isOwner($player->getName()) ){
                        ServerUtils::error($player, "마을장만 사용할 수 있는 기능이에요.");
                        break;
                    }
                    $player->sendForm(new TownCreateBoardForm($town));
                    break;
                case 6:
                    if ( ! $town->isOwner($player->getName()) ){
                        ServerUtils::error($player, "마을장만 사용할 수 있는 기능이에요.");
                        break;
                    }
                    $player->sendForm(new TownAddAssistantForm($town));
                    break;
                case 7:
                    if ( ! $town->isOwner($player->getName()) ){
                        ServerUtils::error($player, "마을장만 사용할 수 있는 기능이에요.");
                        break;
                    }
                    $player->sendForm(new TownRemoveAssistantForm($town));
                    break;
                case 8:
                    if ( ! $town->isOwner($player->getName()) ){
                        ServerUtils::error($player, "마을장만 사용할 수 있는 기능이에요.");
                        break;
                    }
                    $player->sendForm(new TownDeleteForm($town));
                    break;
            }
        });
        $this->setTitle("마을 관리");
        $this->setContent([
            " ",
            "§b§l! §r원하는 기능을 선택해주세요.",
            " "
        ]);
        $this->addButton("주민 초대\n마을에 주민을 초대해요.");
        $this->addButton("주민 추방\n마을로부터 추방해요.");
        $this->addButton("마을 출금\n마을 금고로부터 출금해요.");
        $this->addButton("마을 확장\n마을의 크기를 확장해요.");
        $this->addButton("세금 설정\n마을의 세금을 설정해요.");
        $this->addButton("모집 홍보\n마을을 게시판에 홍보해요.");
        $this->addButton("부마을장 임명\n주민을 부마을장으로 임명해요.");
        $this->addButton("부마을장 임명 해제\n부마을장 임명을 해제해요.");
        $this->addButton("마을 해체\n마을을 해체해요.");
    }

}