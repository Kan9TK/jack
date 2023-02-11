<?php

declare(strict_types=1);

namespace RoMo\WarpCore\form;

use pocketmine\form\Form;
use pocketmine\player\Player;
use RoMo\WarpCore\warp\Warp;
use RoMo\WarpCore\warp\WarpFactory;
use RoMo\WarpCore\WarpCore;

class CreateWarpForm implements Form{
    public function jsonSerialize() : array{
        return [
            "type" => "custom_form",
            "title" => "워프 추가",
            "content" => [
                [
                    "type" => "input",
                    "text" => "이름"
                ],
                [
                    "type" => "toggle",
                    "text" => "타이틀",
                    "default" => true
                ],
                [
                    "type" => "toggle",
                    "text" => "파티클",
                    "default" => true
                ],
                [
                    "type" => "toggle",
                    "text" => "사운드",
                    "default" => true
                ],
                [
                    "type" => "toggle",
                    "text" => "유저 사용",
                    "default" => true
                ],
                [
                    "type" => "toggle",
                    "text" => "명령어 추가",
                    "default" => true
                ]
            ]
        ];
    }
    public function handleResponse(Player $player, $data) : void{
        if($data === null){
            $player->sendForm(new ManageWarpForm());
            return;
        }
        if(!isset($data[0]) || $data[0] == ""){
            $player->sendMessage("should.do.input.warp.name");
            return;
        }
        $data[0] = (string) $data[0];
        if(WarpFactory::getInstance()->isExistWarp($data[0])){
            $player->sendMessage("already.exist.warp");
            return;
        }
        $warp = new Warp($data[0], $player->getLocation(), $data[1], $data[2], $data[3], $data[4], $data[5]);
        WarpFactory::getInstance()->addWarp($warp);
        $player->sendMessage("성공적으로 ".$warp->getName()." 워프를 추가했어요.");
    }
}