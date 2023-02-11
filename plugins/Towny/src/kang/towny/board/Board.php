<?php

declare(strict_types=1);

namespace kang\towny\board;

use pocketmine\player\Player;

class Board{

    public function __construct(private string $writer, private string $title, private string $text, private int $cool){

    }

    public function getWriter() : string{
        return $this->writer;
    }

    public function getTitle() : string{
        return $this->title;
    }

    public function getText() : string{
        return $this->text;
    }

    public function getCool() : int{
        return $this->cool;
    }

    public function jsonSerialize(){
        return [
            "writer"=>$this->getWriter(),
            "title"=>$this->getTitle(),
            "text"=>$this->getText(),
            "cool"=>$this->getCool()
        ];
    }

    public static function jsonDeSerialize($boardData){
        return new Board($boardData["writer"],$boardData["title"],$boardData["text"],$boardData["cool"]);
    }

}