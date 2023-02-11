<?php

declare(strict_types=1);

namespace kang\towny\command\subcommand;

use CortexPE\Commando\BaseSubCommand;
use kang\ServerUtils\ServerUtils;
use kang\towny\Towny;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class TownAcceptCommand extends BaseSubCommand{

    public function prepare(): void
    {
        $this->setPermission("true");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ( ! $sender instanceof Player) return;

        $plugin = Towny::getInstance();

        $sessionManager = $plugin->getSessionManager();
        if ( $sessionManager->getInvitation($sender) == null ){
            ServerUtils::error($sender, "초대장이 없어요.");
            return;
        }
        $townManager = $plugin->getTownManager();
        if ( $townManager->getTownByName($sender->getName()) !== null ){
            ServerUtils::error($sender, "이미 마을에 가입되어 있어요.");
            return;
        }

        $town = $sessionManager->getInvitation($sender);
        $town->addMember($sender);
        $town->broadMsg($sender->getName()."님이 마을의 주민이 되었어요. 환영해주세요!");

        $sessionManager->deleteInvitation($sender);
    }

}