<?php

declare(strict_types=1);

namespace RoMo\WarpCore\form;

use pocketmine\form\Form;
use pocketmine\player\Player;
use RoMo\WarpCore\warp\Warp;
use RoMo\WarpCore\warp\WarpFactory;
use RoMo\WarpCore\WarpCore;

class EditWarpListForm implements Form{

    /** @var Warp[] */
    private array $warpsForButton;

    public function jsonSerialize() : array{
        $buttons = [];
        foreach(WarpFactory::getInstance()->getAllWarp() as $warp){
            $this->warpsForButton[] = $warp;
            $buttons[] = ["text" => $warp->getName()." 워프를 수정해요."];
        }
        return [
            "type" => "form",
            "title" => "워프 수정",
            "content" => "",
            "buttons" => $buttons
        ];
    }
    public function handleResponse(Player $player, $data) : void{
        if($data === null){
            $player->sendForm(new ManageWarpForm());
            return;
        }
        if(!isset($this->warpsForButton[$data])){
            $player->sendMessage("해당 워프를 찾을 수 없어요.");
            return;
        }
        $warp = $this->warpsForButton[$data];
        $player->sendForm(new EditWarpForm($warp));
    }
}