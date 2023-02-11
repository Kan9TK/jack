<?php

declare(strict_types=1);

namespace kang\trashcan\inventory;

use muqsit\invmenu\InvMenu;
use pocketmine\player\Player;

class TrashCanInventory{

    protected InvMenu $inv;

    public function __construct(){
        $this->inv = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $this->inv->setName("쓰레기통");
    }

    public function sendTo(Player $player) : void{
        $this->inv->send($player);
    }

}