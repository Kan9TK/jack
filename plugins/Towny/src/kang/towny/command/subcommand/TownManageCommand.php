<?php

declare(strict_types=1);

namespace kang\towny\command\subcommand;

use CortexPE\Commando\BaseSubCommand;
use kang\ServerUtils\ServerUtils;
use kang\towny\form\TownManageForm;
use kang\towny\Towny;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class TownManageCommand extends BaseSubCommand{

    public function prepare(): void{
        $this->setPermission("true");
    }
    
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ( $sender instanceof Player ){
            $plugin = Towny::getInstance();
            if ( ($town = $plugin->getTownManager()->getTownByName($sender->getName())) == null ){
                ServerUtils::error($sender, "마을에 소속되어 있지 않아요.");
                return;
            }
            if ( ! $town->isOwner($sender->getName()) && $town->isAssistant($sender->getName()) ){
                ServerUtils::error($sender, "마을장과 부마을장만 사용 가능한 명령어에요.");
                return;
            }
            $sender->sendForm(new TownManageForm($town));
        }
    }

}