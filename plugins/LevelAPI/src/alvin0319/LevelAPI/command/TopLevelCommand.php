<?php

declare(strict_types=1);

namespace alvin0319\LevelAPI\command;

use alvin0319\LevelAPI\LevelAPI;
use kang\CommandLibrary\BaseCommand;
use kang\ServerUtils\ServerUtils;
use pocketmine\command\CommandSender;

class TopLevelCommand extends BaseCommand{

	public function __construct(private LevelAPI $plugin){
		parent::__construct($plugin, "레벨순위", "레벨 순위를 확인합니다.");
	}

    public function prepare(): void{
        $this->setPermission("true");
    }

	public function onExecute(CommandSender $player, string $commandLabel, array $args) : bool{
	    $page = $args[0] ?? 1;
		ServerUtils::sendRanking($player, $this->plugin->getAll(), "레벨", $page, 5, "LV");
		return true;
	}
}