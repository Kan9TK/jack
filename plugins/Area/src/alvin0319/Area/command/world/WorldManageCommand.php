<?php

declare(strict_types=1);

namespace alvin0319\Area\command\world;

use alvin0319\Area\AreaLoader;
use alvin0319\Area\world\WorldData;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use kang\ServerUtils\ServerUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class WorldManageCommand extends BaseCommand{
    
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!$sender instanceof Player){
            return;
        }
        $data = AreaLoader::getInstance()->getWorldManager()->get($sender->getWorld());
        switch($args["타입"] ?? "x") {

            case "보호":
                $v = $data->get(WorldData::PROTECT);
                $data->set(WorldData::PROTECT, !$v);
                ServerUtils::msg($sender, "{$sender->getWorld()->getFolderName()} 월드의 보호를 " . ($v ? "비" : "") . "허용으로 설정했습니다.");
                break;

            case "pvp":
            case "전투":
                $v = $data->get(WorldData::PVP);
                $data->set(WorldData::PVP, !$v);
                ServerUtils::msg($sender, "{$sender->getWorld()->getFolderName()} 월드의 유저간 전투를 " . ($v ? "비" : "") . "허용으로 설정했습니다.");
                break;

            case "인벤세이브":
            case "인벤토리세이브":
                $v = $data->get(WorldData::INVENTORY_SAVE);
                $data->set(WorldData::INVENTORY_SAVE, !$v);
                ServerUtils::msg($sender, "{$sender->getWorld()->getFolderName()} 월드의 인벤토리 세이브를 " . ($v ? "비" : "") . "허용으로 설정했습니다.");
                break;

            case "아이템드랍":
                $v = $data->get(WorldData::ITEM_DROP);
                $data->set(WorldData::ITEM_DROP, !$v);
                ServerUtils::msg($sender, "{$sender->getWorld()->getFolderName()} 월드의 아이템 드랍을 " . ($v ? "비" : "") . "허용으로 설정했습니다.");
                break;

            default:
                foreach ([
                             ["보호", "월드의 보호를 설정합니다."],
                             ["유저간전투허용", "월드의 전투를 설정합니다."],
                             ["인벤토리세이브", "월드의 인벤토리 세이브를 설정합니다."],
                             ["아이템드랍", "월드의 아이템 드랍을 설정합니다."]
                         ] as $usage) {
                    ServerUtils::msg($sender, "/월드관리 " . $usage[0] . " - " . $usage[1]);
                }

                $protect = ($data->get(WorldData::PROTECT) ? "" : "비") . "허용";
                $pvp = ($data->get(WorldData::PVP) ? "" : "비") . "허용";
                $inventory_save = ($data->get(WorldData::INVENTORY_SAVE) ? "" : "비") . "허용";
                $item_drop = ($data->get(WorldData::ITEM_DROP) ? "" : "비") . "허용";

                $sender->sendMessage("§a- - - - - - - - - -");
                $sender->sendMessage($sender->getWorld()->getFolderName() . " 월드의 설정입니다.");
                $sender->sendMessage("보호: {$protect}");
                $sender->sendMessage("전투: {$pvp}");
                $sender->sendMessage("인벤토리 세이브: {$inventory_save}");
                $sender->sendMessage("아이템 드랍: {$item_drop}");
                $sender->sendMessage("§a- - - - - - - - - -");
        }
    }

    public function prepare(): void{
        $this->setPermission("op");
        $this->registerArgument(0, new RawStringArgument("타입", true));
    }

}