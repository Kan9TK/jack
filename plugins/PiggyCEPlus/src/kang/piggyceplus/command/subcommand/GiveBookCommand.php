<?php

declare(strict_types=1);

namespace kang\piggyceplus\command\subcommand;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\BaseSubCommand;
use kang\piggyceplus\util\CEBookUtil;
use kang\ServerUtils\ServerUtils;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\player\Player;

class GiveBookCommand extends BaseSubCommand{

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!$sender instanceof Player)return;
        if(count($args) < 2){
            return;
        }
        $item = CEBookUtil::createBook($args["id"],$args["level"],$args["success"]??100);
        if ( $item instanceof Item ) {
            $sender->getInventory()->addItem($item);
            ServerUtils::msg($sender, "성공적으로 지급되었어요.");
        }else{
            ServerUtils::msg($sender, "존재하지 않는 인첸트예요.");
        }
    }

    protected function prepare(): void
    {
        $this->setPermission("op");
        $this->registerArgument(0, new IntegerArgument("id", true));
        $this->registerArgument(1, new IntegerArgument("level", true));
        $this->registerArgument(2, new IntegerArgument("success", true));
    }

}