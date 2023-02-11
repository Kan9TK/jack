<?php

declare(strict_types=1);

namespace alvin0319\Friend\form;

use alvin0319\Friend\Friend;
use kang\ServerUtils\ServerUtils;
use OnixUtils\OnixUtils;
use pocketmine\form\Form;
use pocketmine\player\Player;
use function trim;

class FriendRequestForm implements Form{

	public function jsonSerialize() : array{
		return [
			"type" => "custom_form",
			"title" => "Friend - Master",
			"content" => [
				[
					"type" => "input",
					"text" => "신청할 친구의 닉네임을 적어주세요.",
					"placeholder" => "ex) steve123"
				]
			]
		];
	}

	public function handleResponse(Player $player, $data) : void{
		$friend = $data[0] ?? "";
		if(trim($friend) === ""){
			return;
		}
		if(!Friend::getInstance()->hasData($friend)){
            ServerUtils::error($player, "해당 플레이어의 정보를 찾을 수 없어요.");
			return;
		}
		if(Friend::getInstance()->isFriend($player, $friend)){
            ServerUtils::error($player, "해당 플레이어와 이미 친구에요.");
			return;
		}
		if(Friend::getInstance()->isQueue($friend, $player)){
            ServerUtils::error($player, "이미 해당 플레이어에게 친구 신청을 보냈어요.");
			return;
		}
		Friend::getInstance()->addQueue($friend, $player);
        ServerUtils::msg($player, "성공적으로 {$friend}§f님에게 친구 요청을 보냈어요.");
	}
}