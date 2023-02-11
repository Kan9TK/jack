<?php

declare(strict_types=1);

namespace alvin0319\Area\command\area\subcommand;

use alvin0319\Area\area\AreaProperties;
use alvin0319\Area\AreaLoader;
use CortexPE\Commando\BaseSubCommand;
use kang\ServerUtils\ServerUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class AreaSettingCommand extends BaseSubCommand {

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
        switch($args[0] ?? "x"){
            case "pvp":
            case "전투":
                $v = $area->getAreaProperties()->get(AreaProperties::PVP);
                $area->getAreaProperties()->set(AreaProperties::PVP, !$v);
                ServerUtils::msg($sender, "땅의 유저간전투허용을 " . ($v ? "비" : "") . "허용으로 바꿨습니다.");
                break;
            case "보호":
                $v = $area->getAreaProperties()->get(AreaProperties::PROTECT);
                $area->getAreaProperties()->set(AreaProperties::PROTECT, !$v);
                ServerUtils::msg($sender, "땅의 보호를 " . ($v ? "비" : "") . "보호로 바꿨습니다.");
                break;
            case "인벤토리세이브":
                $v = $area->getAreaProperties()->get(AreaProperties::INVENTORY_SAVE);
                $area->getAreaProperties()->set(AreaProperties::INVENTORY_SAVE, !$v);
                ServerUtils::msg($sender, "땅의 인벤토리 세이브를 " . ($v ? "비" : "") . "허용으로 바꿨습니다.");
                break;
            default:
                foreach([
                            ["유저간전투허용", "유저간 전투 허용을 관리합니다."],
                            ["보호", "땅의 보호를 관리합니다."],
                            ["인벤토리세이브", "땅의 인벤토리 세이브를 관리합니다."]
                        ] as $usage){
                    ServerUtils::msg($sender, "/땅 설정 " . $usage[0] . " - " . $usage[1]);
                }
        }
    }

    public function prepare(): void
    {
        $this->setPermission("op");
    }
	
}