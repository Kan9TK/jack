<?php

declare(strict_types=1);

namespace kang\towny\command;

use CortexPE\Commando\BaseCommand;
use kang\towny\command\subcommand\TownAcceptCommand;
use kang\towny\command\subcommand\TownBoardCommand;
use kang\towny\command\subcommand\TownManageCommand;
use kang\towny\form\TownHaveForm;
use kang\towny\form\TownNoHaveForm;
use kang\towny\Towny;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class TownCommand extends BaseCommand {

    public function prepare(): void{
        $this->setPermission("true");
        $this->registerSubCommand(new TownManageCommand("관리", "마을 관리 명령어예요."));
        $this->registerSubCommand(new TownBoardCommand("게시판", "마을 게시판 명령어예요."));
        $this->registerSubCommand(new TownAcceptCommand("초대수락" ,"마을 초대를 수락해요."));
    }
    
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ($sender instanceof Player) {
            if (!isset($args[0])) {
                $plugin = Towny::getInstance();
                if ($plugin->getTownManager()->getTownByName($sender->getName()) == null) {
                    $sender->sendForm(new TownNoHaveForm());
                } else {
                    $sender->sendForm(new TownHaveForm());
                }
            }
        }
    }

}