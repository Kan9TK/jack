<?php

declare(strict_types=1);

namespace ne0sw0rld\command;


use kang\CommandLibrary\BaseCommand;
use kang\ServerUtils\ServerUtils;
use pocketmine\command\CommandSender;

use ne0sw0rld\PlayingTime;

class TimeCheckC extends BaseCommand
{


    public function __construct (private PlayingTime $plugin){
		parent::__construct ($plugin, '접속시간', '나 혹은 다른 유저의 접속시간을 확인합니다.', '접속시간 확인 [닉네임]', []);
    }

    public function prepare(): void{
        $this->setPermission("true");
    }

    public function onExecute (CommandSender $player, string $commandLabel, array $args) : bool
    {

		$target = strtolower (($args[0] ?? $player->getName()));

		if (! isset ($this->plugin->playerD[$target])) {
			ServerUtils::msg($player, '해당 플레이어의 정보를 찾을 수 없어요.');
			return true;
		}

        ServerUtils::msg($player, $target . '님의 누적 접속 시간: ' . $this->plugin->getKoreanTime ($target));
		return true;

    }

}