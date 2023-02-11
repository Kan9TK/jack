<?php
declare(strict_types=1);

namespace Texter\form;

use jojoe77777\FormAPI\CustomForm;
use kang\ServerUtils\ServerUtils;
use pocketmine\form\Form;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use Texter\text\Text;
use Texter\Texter;

class EditForm implements Form{

    public function jsonSerialize() : array{
        $list = Texter::getInstance()->getTexts();
        $arr = [];
        foreach($list as $text){
            $arr[] = ["text" => Texter::toString($text->getPosition()) . "\n" . $text->getText() . "..."];
        }

        return [
            "type" => "form",
            "title" => "텍스트 수정하기",
            "content" => "수정할 태그를 선택해주세요.",
            "buttons" => $arr
        ];
    }

    public function handleResponse(Player $player, $data) : void{
        if($data !== null){
            $arr = [];
            foreach(Texter::getInstance()->getTexts() as $text)
                $arr[] = $text;
            $text = Texter::getInstance()->getText(Texter::toString($arr[$data]->getPosition()));
            $player->sendForm($this->EditForm($text));
        }
    }

    public function EditForm(Text $text) : CustomForm{
        $form = new CustomForm(function(Player $player, $data) use($text){
            if(!isset($data))return;

            array_shift($data);

            $string = array_filter($data, function($var){
                return $var !== "";
            });

            $string = implode("\n".TextFormat::RESET, $string);
            $string = str_replace("\n".TextFormat::RESET."(x)", "\n \n", $string);

            if ( $text == "" ){
                return;
            }

            $text->setText($string);
            Texter::getInstance()->updateText($text);
            ServerUtils::msg($player, "성공적으로 텍스트를 수정했어요.");

        });

        $form->setTitle("텍스트 수정하기");
        $form->addLabel("줄 비우기: (x)");

        $text = str_replace("\n \n", "\n".TextFormat::RESET."(x)", $text->getText());
        $text = explode("\n".TextFormat::RESET, $text);
        for($i=0;$i<=10;$i++){
            $line = $text[$i] ?? "";
            $form->addInput($i."번째 줄", "", $line);
        }

        return $form;
    }
}