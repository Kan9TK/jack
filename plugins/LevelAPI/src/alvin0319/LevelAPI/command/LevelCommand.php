<?php

declare(strict_types=1);

namespace alvin0319\LevelAPI\command;

use alvin0319\LevelAPI\LevelAPI;
use kang\CommandLibrary\BaseCommand;
use kang\ServerUtils\ServerUtils;
use pocketmine\command\CommandSender;

class LevelCommand extends BaseCommand{

	public function __construct(private LevelAPI $plugin){
		parent::__construct($plugin, "레벨", "레벨을 확인합니다.");
	}

	public function prepare(): void{
        $this->setPermission("true");
    }

    public function onExecute(CommandSender $player, string $commandLabel, array $args): bool
    {
        if (count($args) > 0) {
            $name = array_shift($args) ?? "";
            if (!$this->plugin->hasData($name)) {
                ServerUtils::error($player, "해당 플레이어의 정보를 찾을 수 없습니다.");
                return true;
            }
            ServerUtils::msg($player, $name . "님의 레벨: " . $this->plugin->getLevel($name));
            return true;
        }

        ServerUtils::msg($player, "내 레벨: " . $this->plugin->getLevel($player));
        return true;
    }
}