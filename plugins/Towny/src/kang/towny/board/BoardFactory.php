<?php

declare(strict_types=1);

namespace kang\towny\board;

use kang\towny\setting\Setting;
use kang\towny\Towny;
use pocketmine\player\Player;

class BoardFactory{

    public function __construct(private Towny $plugin){

    }

    public static function createBoard(Player $player, string $title, string $text) : Board{
        return new Board($player->getName(), $title, $text, time() + Setting::BOARD_COOL);
    }

}