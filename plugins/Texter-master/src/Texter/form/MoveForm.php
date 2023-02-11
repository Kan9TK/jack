<?php
declare(strict_types=1);

namespace Texter\form;

use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\ModalForm;
use kang\ServerUtils\ServerUtils;
use pocketmine\form\Form;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use Texter\text\Text;
use Texter\Texter;

class MoveForm implements Form{

    public function jsonSerialize() : array{
        $list = Texter::getInstance()->getTexts();
        $arr = [];
        foreach($list as $text){
            $arr[] = ["text" => Texter::toString($text->getPosition()) . "\n" . $text->getText() . "..."];
        }

        return [
            "type" => "form",
            "title" => "텍스트 이동하기",
            "content" => "이동시킬 태그를 선택해주세요.",
            "buttons" => $arr
        ];
    }

    public function handleResponse(Player $player, $data) : void{
        if($data !== null){
            $arr = [];
            foreach(Texter::getInstance()->getTexts() as $text)
                $arr[] = $text;
            $text = Texter::getInstance()->getText(Texter::toString($arr[$data]->getPosition()));
            $player->sendForm($this->MoveForm($text));
        }
    }

    public function MoveForm(Text $text) : ModalForm{
        $form = new ModalForm(function(Player $player, $data) use($text){
            if(!isset($data))return;
            Texter::getInstance()->moveText(Texter::toString($text->getPosition()), $player->getPosition());
            ServerUtils::msg($player, "성공적으로 이동시켰어요.");
        });

        $form->setTitle("텍스트 이동하기");
        $form->setContent(" \n해당 위치로 이동시킬까요?\n ");
        $form->setButton1("이동시킬게요.");
        $form->setButton2("취소할게요.");

        return $form;
    }
}