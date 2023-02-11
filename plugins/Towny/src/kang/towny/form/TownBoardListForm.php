<?php

declare(strict_types=1);

namespace kang\towny\form;

use jojoe77777\FormAPI\SimpleForm;
use kang\towny\town\Town;
use kang\towny\board\Board;
use kang\towny\Towny;
use pocketmine\player\Player;

class TownBoardListForm extends SimpleForm{
    
    public function __construct()
    {
        parent::__construct(function(Player $player, $data){
            if(!isset($data))return;

            $boards = [];
            foreach ( Towny::getInstance()->getBoardManager()->getAllBoards() as $writerName => $board ){
                if ( $board instanceof Board ){
                    $boards[] = $board;
                }
            }

            $board = $boards[$data];
            $player->sendForm(new TownBoardForm($board));

        });

        $plugin = Towny::getInstance();
        $boardManager = $plugin->getBoardManager();

        $this->setTitle("모집 게시판");
        $this->setContent([
            " ",
            "§b§l! §r모집 글은 디스코드에서도 확인할 수 있어요.",
            "§b§l! §r총 ".count($boardManager->getAllBoards())."개의 모집 글이 존재해요.",
            " "
        ]);
        foreach ( Towny::getInstance()->getBoardManager()->getAllBoards() as $writerName => $board ){
            if ( $board instanceof Board ){
                $town = Towny::getInstance()->getTownManager()->getTownByName($writerName);
                if ( $town instanceof Town){
                    $this->addButton($board->getTitle()."\n".$town->getName()." 마을");
                }
            }
        }
    }

}