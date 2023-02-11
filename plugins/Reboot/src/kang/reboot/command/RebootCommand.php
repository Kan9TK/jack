<?php

declare(strict_types=1);

namespace kang\reboot\command;

use CortexPE\Commando\BaseCommand;
use kang\reboot\command\subcommand\RebootDelayCommand;
use kang\reboot\command\subcommand\RebootStartCommand;
use kang\reboot\Reboot;
use kang\ServerUtils\ServerUtils;
use pocketmine\command\CommandSender;

class RebootCommand extends BaseCommand{

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ( ! isset ( $args[0] ) ){
            $time = ServerUtils::TimeToString(Reboot::getInstance()->getLeftTime());
            ServerUtils::msg($sender, "재부팅까지 {$time} 남았어요.");
        }
    }

    protected function prepare(): void
    {
        $this->setPermission("true");
        $this->registerSubCommand(new RebootStartCommand("시작", "재부팅을 실행해요."));
        $this->registerSubCommand(new RebootDelayCommand("주기", "재부팅 주기를 설정해요."));
    }

}