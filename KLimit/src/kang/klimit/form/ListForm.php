<?php 

declare(strict_types=1);

namespace kang\klimit\form;

use pocketmine\form\Form;
use kang\klimit\KLimit;

class ListForm implements Form{
	
	public function __construct(protected KLimit $plugin){
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
			"title" => "기간제 목록",
			"content" => "",
			"buttons" => $buttons
		];
	}

	public function handleResponse(Player $player, $data) : void{
		if(!isset($data))return;
		$buttons = [];
		foreach ( $this->plugin->data as $name => $dataa ){
			$buttons[] = $name;
		}
		$button = $buttons[$data];
		$player->sendForm(new ManageForm($this->plugin, $button));
	}
	
}