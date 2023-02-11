<?php

declare(strict_types=1);

namespace RoMo\WarpCore\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use RoMo\WarpCore\warp\WarpFactory;
use RoMo\WarpCore\WarpCore;

class WarpCommand extends Command{
    public function __construct(){
        parent::__construct("워프", "워프 명령어예요.");
        $this->setPermission("use-warp");
    }
    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender instanceof Player){
            $sender->sendMessage("in-game");
            return;
        }
        if(!isset($args[0])){
            //TODO: 목록 출력
            return;
        }
        if(!WarpFactory::getInstance()->isExistWarp($args[0])){
            $sender->sendMessage("해당 이름의 워프를 찾을 수 없어요.");
            return;
        }
        $warp = WarpFactory::getInstance()->getWarp($args[0]);
        /*if(!$warp->isCommandRegister()){
            $sender->sendMessage("해당 워프를 ");
            return;
        }*/
        $warp->teleport($sender);
    }
}