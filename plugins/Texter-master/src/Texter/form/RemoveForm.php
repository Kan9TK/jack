<?php
declare(strict_types=1);

namespace Texter\form;

use pocketmine\form\Form;
use pocketmine\player\Player;
use Texter\Texter;

class RemoveForm implements Form{

	public function jsonSerialize() : array{
		$list = Texter::getInstance()->getTexts();

		$arr = [];

		foreach($list as $text){
			$arr[] = ["text" => Texter::toString($text->getPosition()) . "\n" . $text->getText() . "..."];
		}

		return [
			"type" => "form",
			"title" => "텍스트 제거하기",
			"content" => "제거할 태그를 선택해주세요.",
			"buttons" => $arr
		];
	}

	public function handleResponse(Player $player, $data) : void{
		if($data !== null){
			$arr = [];

			foreach(Texter::getInstance()->getTexts() as $text)
				$arr[] = $text;

			Texter::getInstance()->removeText(Texter::toString($arr[$data]->getPosition()));
			$player->sendMessage(Texter::$prefix . "제거되었습니다.");
		}
	}
}