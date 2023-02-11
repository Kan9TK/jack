<?php

declare(strict_types=1);

namespace kang\mailbox\command;

use CortexPE\Commando\BaseCommand;
use kang\mailbox\inventory\MailBoxInventory;
use kang\mailbox\MailBox;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

class MailBoxCommand extends BaseCommand{

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!$sender instanceof Player)return;
        $inv = new MailBoxInventory($sender);
        $inv->send();
    }

    protected function prepare(): void
    {
        $this->setPermission("true");
    }

}