<?php

declare(strict_types=1);

namespace kang\towny\board;

use kang\dataconfig\DataConfig;
use kang\towny\Towny;

class BoardManager{

    private DataConfig $boardsConfig;
    private array $boards = [];

    public function __construct(private Towny $plugin){
        $this->boardsConfig = new DataConfig($this->plugin->getDataFolder()."boards.json",DataConfig::TYPE_JSON,[]);
        $this->loadAllBoards();
    }

    public function loadBoard(Board $board) : void{
        $this->boards[$board->getWriter()] = $board;
    }

    public function loadAllBoards() : void{
        foreach ( $this->boardsConfig->data as $writerName => $boardData ){
            $this->boards[$writerName] = Board::jsonDeSerialize($boardData);
        }
    }

    public function getBoard(string $writerName) : ?Board{
        if ( isset ( $this->boards[$writerName] ) ){
            $board = $this->boards[$writerName];
            if ( $board instanceof Board ) {
                if (time() > $board->getCool()) {
                    unset($this->boards[$writerName]);
                }
            }
        }
        return $this->boards[$writerName] ?? null;
    }

    public function getAllBoards() : array{
        foreach ( $this->boards as $writerName => $board ){
            if ( $board instanceof Board ){
                if (time() > $board->getCool()){
                    unset($this->boards[$writerName]);
                }
            }
        }
        return $this->boards;
    }

    public function save() : void{
        foreach ( $this->getAllBoards() as $writerName => $board ){
            if ( $board instanceof Board) {
                $this->boardsConfig->set($writerName, $board->jsonSerialize());
            }
        }
        $this->boardsConfig->save($this->boardsConfig->data);
    }

}