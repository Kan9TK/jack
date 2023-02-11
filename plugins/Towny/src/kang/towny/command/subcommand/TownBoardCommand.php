<?php

declare(strict_types=1);

namespace kang\towny\command\subcommand;

use CortexPE\Commando\BaseSubCommand;
use kang\towny\form\TownBoardListForm;
use kang\towny\Towny;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class TownBoardCommand extends BaseSubCommand{

    public function prepare(): void
    {
        $this->setPermission("true");
    }
    
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ( ! $sender instanceof Player ) return;
        $sender->sendForm(new TownBoardListForm());
    }

}