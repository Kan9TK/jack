<?php

declare(strict_types=1);

namespace alvin0319\Area\command\area;

use alvin0319\Area\command\area\subcommand\AreaInfoCommand;
use alvin0319\Area\command\area\subcommand\AreaMoveCommand;
use alvin0319\Area\command\area\subcommand\AreaSettingCommand;
use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;

class AreaCommand extends BaseCommand{

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        // TODO: Implement onRun() method.
    }

    public function prepare(): void
    {
        $this->setPermission("op");
        $this->registerSubCommand(new AreaInfoCommand("정보", "땅의 정보를 확인합니다."));
        $this->registerSubCommand(new AreaMoveCommand("이동", "땅으로 이동합니다."));
        $this->registerSubCommand(new AreaSettingCommand("설정", "땅을 설정합니다."));
    }

}