<?php

declare(strict_types=1);

namespace kang\itemeditor\command;

use CortexPE\Commando\BaseCommand;
use kang\itemeditor\form\MainForm;
use kang\ServerUtils\ServerUtils;
use pocketmine\command\CommandSender;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class ItemEditorCommand extends BaseCommand{

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ( ! $sender instanceof Player ) return;
        $item = $sender->getInventory()->getItemInHand();
        if ( $item->getId() == ItemIds::AIR ){
            ServerUtils::error($sender, "공기는 수정할 수 없어요.");
            return;
        }
        $sender->sendForm(new MainForm($item));
    }

    protected function prepare(): void
    {
        $this->setPermission("true");
    }

}