<?php

declare(strict_types=1);

namespace kang\wild\command;

use kang\CommandLibrary\BaseCommand;
use kang\wild\form\TeleportNormalWildForm;
use kang\wild\Wild;
use pocketmine\command\CommandSender;

class NormalWildCommand extends BaseCommand{

    public function __construct(private Wild $plugin)
    {
        parent::__construct($plugin, "일반야생", "일반 야생 명령어입니다.");
    }

    public function prepare(): void
    {
        $this->setPermission("true");
    }

    public function onExecute(CommandSender $player, string $commandLabel, array $args): bool
    {
        $player->sendForm(new TeleportNormalWildForm());
        return true;
    }

}