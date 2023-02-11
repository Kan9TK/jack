<?php

declare(strict_types=1);

namespace kang\towny\form;

use jojoe77777\FormAPI\SimpleForm;

use kang\towny\board\Board;
use kang\towny\Towny;
use pocketmine\player\Player;

class TownBoardForm extends SimpleForm{

    public function __construct(Board $board)
    {
        parent::__construct(function(Player $player, $data){
            if(!isset($data))return;
        });

        $plugin = Towny::getInstance();
        $writer = $board->getWriter();
        $town = $plugin->getTownManager()->getTownByName($writer);
        $townName = $town->getName();

        $this->setTitle($townName." 마을");
        $this->setContent([
            " ",
            "§b§l▶ §r".$board->getTitle()." §b§l◀",
            "\n§b§l• §r글쓴이: ".$board->getWriter(),
            "\n§b§l• §r".$board->getText(),
            " "
        ]);
    }

}