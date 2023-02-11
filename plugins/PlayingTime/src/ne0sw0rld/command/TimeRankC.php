<?php

declare(strict_types=1);

namespace ne0sw0rld\command;

use kang\CommandLibrary\BaseCommand;
use pocketmine\command\CommandSender;

use ne0sw0rld\PlayingTime;
use kang\ServerUtils\ServerUtils;


class TimeRankC extends BaseCommand
{


	public function __construct (private PlayingTime $plugin){
		parent::__construct ($plugin, '접속시간순위', '접속 시간 순위를 확인합니다.', '접속시간순위 [페이지])', []);
	}

    public function prepare(): void{
        $this->setPermission("true");
    }

	public function onExecute (CommandSender $player, string $commandLabel, array $args) : bool
	{

		$data = $this->plugin->updateAllPlayers();
		$newData = [];
        foreach ($data as $k => $v) {
            $newData[$k] = $v;
        }

        $page = $args[0] ?? 1;

		ServerUtils::sendRanking($player, $newData, "접속시간", 5, $page, "", function($value){
		    return $this->plugin->koreanTimeFormat($value);
        });

		
		return true;

	}

}
