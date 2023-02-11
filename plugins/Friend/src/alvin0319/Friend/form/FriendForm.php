<?php

declare(strict_types=1);

namespace alvin0319\Friend\form;

use alvin0319\Friend\Friend;
use kang\ServerUtils\ServerUtils;
use OnixUtils\OnixUtils;
use pocketmine\form\Form;
use pocketmine\player\Player;
use function count;
use function is_int;

class FriendForm implements Form{
	/** @var Player */
	protected Player $player;

	public function __construct(Player $player){
		$this->player = $player;
	}

	public function jsonSerialize() : array{
		return [
			"type" => "form",
			"title" => "친구",
			"content" => "§f현재 온라인인 친구는 §d" . count(Friend::getInstance()->getOnlineFriends($this->player)) . "§f명이에요.",
			"buttons" => [
				["text" => "§l친구 목록\n§r§8친구 목록을 확인해요."],
				["text" => "§l친구 신청\n§r§8친구 신청을 보내요."],
				["text" => "§l친구 삭제\n§r§8친구를 삭제해요."],
				["text" => "§l친구 신청 목록\n§r§8친구 신청 목록을 확인해요."],
				["text" => "§l귓속말 모드\n§r§8귓속말 모드로 전환해요."]
			]
		];
	}

	public function handleResponse(Player $player, $data) : void{
		if(!is_int($data)){
			return;
		}
		switch($data){
			case 0:
				$player->sendForm(new FriendListForm($player));
				break;
			case 1:
				$player->sendForm(new FriendRequestForm());
				break;
			case 2:
				$player->sendForm(new FriendRemoveForm($player));
				break;
			case 3:
				$player->sendForm(new FriendRequestListForm($player));
				break;
			case 4:
				if(Friend::getInstance()->isFriendChat($player)){
					Friend::getInstance()->removeFriendChat($player);
                    ServerUtils::msg($player, "친구 채팅을 비활성화 했습니다.");
				}else{
					$player->sendForm(new FriendChatForm($player));
				}
				break;
		}
	}
}