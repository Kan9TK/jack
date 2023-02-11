<?php

declare(strict_types=1);

namespace kang\towny\form;

use jojoe77777\FormAPI\CustomForm;
use kang\ServerUtils\ServerUtils;
use kang\towny\board\BoardFactory;
use kang\towny\town\Town;
use kang\towny\setting\Setting;
use kang\towny\Towny;
use onebone\economyapi\EconomyAPI;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class TownCreateBoardForm extends CustomForm {

    public function __construct(private Town $town)
    {
        parent::__construct(function(Player $player, $data){

            if(!isset($data[1],$data[2]))return;

            $plugin = Towny::getInstance();

            $boardManager = $plugin->getBoardManager();
            if ( ($board = $boardManager->getBoard($player->getName())) !== null ){
                $left = $board->getCool() - time();
                ServerUtils::error($player, ServerUtils::TimeToString($left)." 후 홍보할 수 있어요.");
                return;
            }

            $economy = EconomyAPI::getInstance();
            $price = Setting::BOARD_PRICE;
            if ( $economy->myMoney($player) < $price ){
                $left = $price - $economy->myMoney($player);
                ServerUtils::error($player, $left."원이 부족하여 홍보할 수 없어요.");
                return;
            }

            $economy->reduceMoney($player, $price);

            $title = $data[1];
            $text = $data[2];

            $text = str_replace("(n)", "\n", $text);

            $board = BoardFactory::createBoard($player, $title, $text);

            $boardManager->loadBoard($board);

            $townManager = Towny::getInstance()->getTownManager();
            $town = $townManager->getTownByName($player->getName());

            ServerUtils::msg($player, "성공적으로 마을 모집 게시판에 홍보 글을 작성했어요. ".ServerUtils::TimeToString(Setting::BOARD_COOL)." 후 사라지는 점 유의해주세요.");
            Server::getInstance()->broadcastMessage("§e§l! §r".$town->getName()." 마을의 홍보 글이 게시되었어요.");

        });

        $this->setTitle("마을 모집 게시판");
        $this->addLabel([
            " ",
            "§b§l! §r모집 홍보 가격은 ".Setting::BOARD_PRICE."원이에요.",
            "§b§l! §r(n)을 입력하여 줄바꿈이 가능해요.",
            "",
            "§c§l! §r모집 홍보는 ".ServerUtils::TimeToString(Setting::BOARD_COOL)." 마다 가능해요.",
            "§c§l! §r올바르지 않은 홍보는 삭제 처리 및 제재 대상이에요.",
            " "
        ]);
        $this->addInput("제목");
        $this->addInput("내용");
    }



}