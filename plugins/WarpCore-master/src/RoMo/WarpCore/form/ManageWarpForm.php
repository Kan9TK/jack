<?php

declare(strict_types=1);

namespace RoMo\WarpCore\form;

use pocketmine\form\Form;
use pocketmine\player\Player;
use RoMo\WarpCore\WarpCore;

class ManageWarpForm implements Form{
    public function jsonSerialize() : array{
        return [
            "type" => "form",
            "title" => "워프 관리",
            "content" => "",
            "buttons" => [
                [
                    "text" => "워프 추가"
                ],
                [
                    "text" => "워프 제거"
                ],
                [
                    "text" => "워프 수정"
                ]
            ]
        ];
    }
    public function handleResponse(Player $player, $data) : void{
        if($data === null){
            return;
        }
        if($data === 0){
            $player->sendForm(new CreateWarpForm());
            return;
        }
        if($data === 1){
            $player->sendForm(new RemoveWarpForm());
            return;
        }
        if($data === 2){
            $player->sendForm(new EditWarpListForm());
            return;
        }
    }
}