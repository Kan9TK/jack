<?php
declare(strict_types=1);

namespace Texter\form;

use pocketmine\form\Form;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use Texter\Texter;
use function trim;

class CreateForm implements Form{

	public function jsonSerialize() : array{
	    
	    $content = [];
        $content[] = ["type" => "label", "text" => "줄 비우기: (x)"];
	    for($i=0;$i<=10;$i++){
	        $content[] = ["type"=>"input","text"=>$i."번째 줄"];
        }
	    
		return [
			"type" => "custom_form",
			"title" => "텍스트 생성하기",
			"content" => $content
		];
	}

	public function handleResponse(Player $player, $data) : void{

	    if(!isset($data))return;

        array_shift($data);

        $text = array_filter($data, function($var){
            return $var !== "";
        });

        $text = implode("\n".TextFormat::RESET, $text);
        $text = str_replace("\n".TextFormat::RESET."(x)", "\n \n", $text);

        if ( $text == "" ){
            return;
        }

		Texter::getInstance()->addText($player->getPosition(), $text);
		$player->sendMessage(Texter::$prefix . "생성되었습니다.");
	}
}