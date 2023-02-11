<?php

declare(strict_types=1);

namespace TeamBixby\CustomCraft\command;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\player\Player;
use TeamBixby\CustomCraft\command\subcommand\AddCraftCommand;
use TeamBixby\CustomCraft\command\subcommand\AddFurnaceCommand;
use TeamBixby\CustomCraft\command\subcommand\ListCraftCommand;
use TeamBixby\CustomCraft\CustomCraft;

class ManageCraftCommand extends BaseCommand {

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void{
    }

	protected function prepare(): void
    {
        $this->setDescription("op");
        $this->registerSubCommand(new AddCraftCommand("조합추가", "add craft"));
        $this->registerSubCommand(new AddFurnaceCommand("화로추가", "add furnace"));
        $this->registerSubCommand(new ListCraftCommand("조합목록", "craft list"));
        //$this->registerSubCommand(new ListFurnaceCommand("화로목록", "furnace list"));
    }
}