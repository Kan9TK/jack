<?php

declare(strict_types=1);

namespace kang\givexp\command;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\TargetArgument;
use CortexPE\Commando\BaseCommand;
use kang\ServerUtils\ServerUtils;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class XPCommand extends BaseCommand{
    
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!isset($args["닉네임"],$args["경험치"]) or !is_numeric($args["경험치"])){
            ServerUtils::msg($sender, "/xp [닉네임] [경험치]");
            return;
        }
        $targetName = $args["닉네임"];
        if ( ($target=Server::getInstance()->getPlayerExact($targetName)) == null ){
            ServerUtils::error($sender, "해당 플레이어가 접속 중이지 않아요.");
            return;
        }
        $target->getXpManager()->addXp($args["경험치"], true);
        ServerUtils::msg($sender, "성공적으로 경험치를 지급했어요.");
    }
    
    protected function prepare(): void
    {
        $this->setPermission("op");
        $this->registerArgument(0, new TargetArgument("닉네임", true));
        $this->registerArgument(1, new IntegerArgument("경험치", true));
    }

}