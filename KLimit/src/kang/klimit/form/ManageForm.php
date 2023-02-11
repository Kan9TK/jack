<?php 

declare(strict_types=1);

namespace kang\klimit\form;

use pocketmine\form\Form;
use kang\klimit\KLimit;
use pocketmine\item\Item;
use pocketmine\Player;

class ManageForm implements Form{
	
	public function __construct(protected KLimit $plugin, protected string $name){
	}

	public function jsonSerialize() : array{
		
		$buttons = [];
		foreach ( $this->plugin->data as $name => $data ){
			$d = $data["d"];
			$h = $data["h"];
			$m = $data["m"];
			$s = $data["s"];
			$buttons[] = ["text"=>$name."\n".$d."일 ".$h."시간 ".$m."분 ".$s."초"];
		}
		
		return [
			"type" => "form",
			"title" => "기간제 관리",
			"content" => "별명: ".$this->name."\n기간: ".$d."일 ".$h."시간 ".$m."분 ".$s."초",
			"buttons" => [
				["text"=>"아이템 받기"],
				["text"=>"아이템 삭제"]
			]
		];
	}

	public function handleResponse(Player $player, $data) : void{
		if(!isset($data))return;
		switch($data){
			case 0:
				$nbt = $this->plugin->data[$this->name]["nbt"];
				$item = Item::jsonDeserialize($nbt);
				$player->getInventory()->addItem($item);
				$player->sendMessage("성공적으로 지급되었습니다.");
				break;
			case 1:
				unset($this->plugin->data[$this->name]);
				$player->sendMessage("성공적으로 삭제했습니다.");
				break;
		}
	}
	
}