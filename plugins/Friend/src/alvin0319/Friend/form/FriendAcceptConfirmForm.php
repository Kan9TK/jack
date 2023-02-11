<?php

declare(strict_types=1);

namespace alvin0319\Friend\form;

use alvin0319\Friend\Friend;
use kang\ServerUtils\ServerUtils;
use OnixUtils\OnixUtils;
use pocketmine\form\Form;
use pocketmine\player\Player;
use function is_bool;

class FriendAcceptConfirmForm implements Form{

	protected Player $player;

	protected string $friend;

	protected int $index;

	public function __construct(Player $player, string $friend, int $index){
		$this->player = $player;
		$this->friend = $friend;
		$this->index = $index;
	}

	public function jsonSerialize() : array{
		return [
			"type" => "modal",
			"title" => "친구 신청",
			"content" => "정말 " . $this->friend . "님의 친구 신청을 수락할까요?",
			"button1" => "수락할게요",
			"button2" => "거절할게요"
		];
	}

	public function handleResponse(Player $player, $data) : void{
		if(!is_bool($data)){
			return;
		}
		Friend::getInstance()->removeQueue($player, $this->index);
		if($data){
			Friend::getInstance()->addFriend($player, $this->friend);
            ServerUtils::msg($player, "성공적으로 {$this->friend}님의 친구 신청을 수락했어요.");
		}else{
            ServerUtils::msg($player, "성공적으로 {$this->friend}님의 친구 신청을 거절했어요.");
		}
	}
}