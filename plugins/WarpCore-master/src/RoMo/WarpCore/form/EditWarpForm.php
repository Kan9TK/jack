<?php

declare(strict_types=1);

namespace RoMo\WarpCore\form;

use pocketmine\form\Form;
use pocketmine\player\Player;
use RoMo\WarpCore\warp\Warp;
use RoMo\WarpCore\WarpCore;

class EditWarpForm implements Form{

    /** @var Warp */
    private Warp $warp;

    public function __construct(Warp $warp){
        $this->warp = $warp;
    }

    public function jsonSerialize() : array{
        return [
            "type" => "custom_form",
            "title" => "워프 수정",
            "content" => [
                [
                    "type" => "toggle",
                    "text" => "타이틀",
                    "default" => $this->warp->isTitle()
                ],
                [
                    "type" => "toggle",
                    "text" => "파티클",
                    "default" => $this->warp->isParticle()
                ],
                [
                    "type" => "toggle",
                    "text" => "사운드",
                    "default" => $this->warp->isSound()
                ],
                [
                    "type" => "toggle",
                    "text" => "유저 사용",
                    "default" => $this->warp->isPermit()
                ],
                [
                    "type" => "toggle",
                    "text" => "명령어 추가",
                    "default" => $this->warp->isCommandRegister()
                ]
            ]
        ];
    }

    public function handleResponse(Player $player, $data) : void{
        if($data === null){
            $player->sendForm(new EditWarpListForm());
            return;
        }
        $this->warp->setIsTitle($data[0]);
        $this->warp->setIsParticle($data[1]);
        $this->warp->setIsSound($data[2]);
        $this->warp->setIsPermit($data[3]);
        $this->warp->setIsCommandRegister($data[4]);
        $player->sendMessage("성공적으로 ".$this->warp->getName()." 워프를 수정했어요.");
    }
}