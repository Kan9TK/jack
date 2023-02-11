<?php

declare(strict_types=1);

namespace alvin0319\Area\command\area\subcommand;

use alvin0319\Area\area\Area;
use alvin0319\Area\AreaLoader;
use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\BaseSubCommand;
use kang\ServerUtils\ServerUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use function array_map;
use function count;
use function implode;
use function is_numeric;

class AreaMoveCommand extends BaseSubCommand {
	
	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!$sender instanceof Player){
            return;
        }
        if(!isset($args["번호"])){
            return;
        }
        if(!is_numeric($args["번호"])){
            return;
        }

        $id = (int)$args["번호"];

        $area = AreaLoader::getInstance()->getAreaManager()->getAreaById($id, $sender->getWorld());
        if($area === null){
            ServerUtils::error($sender, "{$id}번 땅이 존재하지 않습니다.");
            return;
        }
        $area->moveTo($sender);
        ServerUtils::msg($sender, "{$area->getId()}번 땅으로 이동했습니다.");
    }

    public function prepare(): void
    {
        $this->setPermission("op");
        $this->registerArgument(0, new IntegerArgument("번호", true));
    }
	
}