<?php

declare(strict_types=1);

namespace kang\trashcan\form;

use jojoe77777\FormAPI\ModalForm;
use kang\ServerUtils\ServerUtils;
use pocketmine\player\Player;

class ClearInventoryForm extends ModalForm{
    
    public function __construct()
    {
        parent::__construct(function(Player $player, $data){
            if(!isset($data))return;
            switch ($data){
                case true:
                    $player->getInventory()->clearAll();
                    ServerUtils::msg($player, "인벤토리를 모두 청소했어요.");
                    break;
                case false:
                    break;
            }
            
        });
        $this->setTitle("인벤토리 청소");
        $this->setContent([
            " ",
            "! 인벤토리의 아이템이 모두 사라져요.",
            "! 초기화된 아이템은 복구할 수 없어요.",
            "! 정말 인벤토리를 청소하시겠어요?",
            ""
        ]);
        $this->setButton1("청소할게요.");
        $this->setButton2("취소할게요.");
    }

}