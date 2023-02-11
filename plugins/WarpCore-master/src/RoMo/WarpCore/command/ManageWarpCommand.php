<?php

declare(strict_types=1);

namespace RoMo\WarpCore\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use RoMo\WarpCore\form\ManageWarpForm;
use RoMo\WarpCore\WarpCore;

class ManageWarpCommand extends Command{
    public function __construct(){
        parent::__construct("워프관리", "워프를 관리해요.");
        $this->setPermission("manage-warp");
    }
    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender instanceof Player){
            $sender->sendMessage("in-game");
            return;
        }
        $sender->sendForm(new ManageWarpForm());
    }
}