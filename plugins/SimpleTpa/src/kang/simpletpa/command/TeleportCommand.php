<?php

declare(strict_types=1);

namespace kang\simpletpa\command;

use CortexPE\Commando\args\TargetArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use kang\ServerUtils\ServerUtils;
use kang\simpletpa\SimpleTpa;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class TeleportCommand extends BaseCommand{

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!$sender instanceof Player)return;

        if(!isset($args["닉네임"])){
            $this->sendUsage();
            return;
        }
        if( ($target = Server::getInstance()->getPlayerExact($args["닉네임"])) ==null){
            ServerUtils::error($sender, "해당 플레이어가 서버에 접속 중이 아니에요.");
            return;
        }
        if ( strtolower($args["닉네임"]) == strtolower($sender->getName()) ){
            ServerUtils::error($sender, "자기 자신에게 요청할 수 없어요.");
            return;
        }
        SimpleTpa::getInstance()->requestTeleport($sender, $target);
        ServerUtils::msg($sender, "성공적으로 ".$args["닉네임"]."님에게 티피를 요청했어요.");
        ServerUtils::msg($target, $sender->getName()."님이 티피를 요청했어요. 수락하려면 /티피수락 명령어를 입력해주세요.");

    }

    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void
    {
        $this->setPermission("true");
        $this->registerArgument(0, new TargetArgument("닉네임", true));
    }

}