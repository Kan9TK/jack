<?php

declare(strict_types=1);

namespace alvin0319\Area\command\area\subcommand;

use alvin0319\Area\area\AreaProperties;
use alvin0319\Area\AreaLoader;
use CortexPE\Commando\BaseSubCommand;
use kang\ServerUtils\ServerUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use function array_map;
use function implode;

class AreaInfoCommand extends BaseSubCommand {

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!$sender instanceof Player){
            return;
        }

        $area = AreaLoader::getInstance()->getAreaManager()->getArea($sender->getPosition()->asVector3(), $sender->getWorld());
        if($area === null){
            ServerUtils::error($sender, "해당 위치에서 땅을 찾을 수 없습니다.");
            return;
        }

        $pp = $area->getAreaProperties();

        $protect = $pp->get(AreaProperties::PROTECT) ? "허용" : "비허용";
        $pvp = $pp->get(AreaProperties::PVP) ? "허용" : "비허용";
        $inventory_save = $pp->get(AreaProperties::INVENTORY_SAVE) ? "허용" : "비허용";

        $sender->sendMessage("§a- - - - - - - - - -");
        $sender->sendMessage("땅 번호: {$area->getId()}");
        $sender->sendMessage("보호: {$protect}");
        $sender->sendMessage("전투: {$pvp}");
        $sender->sendMessage("인벤토리 세이브: {$inventory_save}");
        $sender->sendMessage("§a- - - - - - - - - -");
    }

    public function prepare(): void
    {
        $this->setPermission("op");
    }
	
}