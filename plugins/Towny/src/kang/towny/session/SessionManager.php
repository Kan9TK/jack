<?php

declare(strict_types=1);

namespace kang\towny\session;

use kang\ServerUtils\ServerUtils;
use kang\towny\town\Town;
use kang\towny\Towny;
use pocketmine\player\Player;

class SessionManager{

    private array $sessions = [];

    public function __construct(private Towny $plugin){
    }

    public function sendInvitation(Town $town, Player $player) : void{
        $this->sessions[$player->getName()]["invite"] = $town;
    }

    public function deleteInvitation(Player $player) : void{
        if ( isset ( $this->sessions[$player->getName()]["invite"] ) ){
            unset($this->sessions[$player->getName()]["invite"]);
        }
    }

    public function getInvitation(Player $player) : ?Town{
        return $this->sessions[$player->getName()]["invite"] ?? null;
    }

}