<?php

declare(strict_types=1);

namespace kang\wild\command;

use kang\CommandLibrary\BaseCommand;
use kang\wild\form\ResetWildForm;
use kang\wild\Wild;
use pocketmine\command\CommandSender;

class ResetWildCommand extends BaseCommand{

    public function __construct(private Wild $plugin)
    {
        parent::__construct($plugin, "야생초기화", "야생 초기화 명령어입니다.");
    }

    public function prepare(): void
    {
        $this->setPermission("op");
    }

    public function onExecute(CommandSender $player, string $commandLabel, array $args): bool
    {
        $player->sendForm(new ResetWildForm());
        return true;
    }

}