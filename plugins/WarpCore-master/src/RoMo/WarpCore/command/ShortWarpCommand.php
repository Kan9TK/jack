<?php

namespace RoMo\WarpCore\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use RoMo\WarpCore\warp\Warp;
use RoMo\WarpCore\WarpCore;

class ShortWarpCommand extends Command{

    /** @var Warp */
    private Warp $warp;

    public function __construct(Warp $warp){
        $this->warp = $warp;
        parent::__construct($this->warp->getName(),$this->warp->getName()." (으)로 워프해요.");
        $this->setPermission("use-warp");
    }
    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if(!$sender instanceof Player){
            $sender->sendMessage("in-game");
            return;
        }
        $this->warp->teleport($sender);
    }
}