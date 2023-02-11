<?php

declare(strict_types=1);

namespace kang\reboot\command\subcommand;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\BaseSubCommand;
use kang\reboot\Reboot;
use kang\ServerUtils\ServerUtils;
use pocketmine\command\CommandSender;

class RebootDelayCommand extends BaseSubCommand{

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!isset($args["숫자"])or!is_numeric($args["숫자"])){
            return;
        }
        $delay = (int)$args["숫자"];
        Reboot::getInstance()->setDelay($delay);
        ServerUtils::msg($sender, "재부팅 주기를 {$delay}시간으로 설정했어요.");
    }

    protected function prepare(): void
    {
        $this->setPermission("op");
        $this->registerArgument(0, new IntegerArgument("숫자(시간)"));
    }

}