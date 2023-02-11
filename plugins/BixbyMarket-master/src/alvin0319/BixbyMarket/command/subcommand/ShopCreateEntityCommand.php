<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\command\subcommand;

use alvin0319\BixbyMarket\BixbyMarket;
use alvin0319\BixbyMarket\category\Category;
use alvin0319\BixbyMarket\shop\Shop;
use kang\CommandLibrary\SubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use function count;
use function implode;

final class ShopCreateEntityCommand extends SubCommand {

    public function __construct(){
        parent::__construct("상점npc", "상점 NPC를 소환해요.", "상점npc [이름] [상점]");
    }

    public function prepare(): void
    {
        $this->setPermission("op");
    }

    public function onExecute(CommandSender $player, string $commandLabel, array $args) : bool{
        if(!$player instanceof Player){
            return true;
        }
        if(count($args) < 2){
            return false;
        }

        $npcName = $args[0];
        $shopName = $args[1];

        if(BixbyMarket::getInstance()->getShopManager()->getShop($shopName) == null){
            $player->sendMessage(BixbyMarket::$prefix . "해당 이름의 상점이 존재하지 않아요.");
            return true;
        }

        BixbyMarket::getInstance()->getEntityManager()->createEntity($player, $npcName, $shopName);
        $player->sendMessage(BixbyMarket::$prefix . "성공적으로 소환했어요.");
        return true;
    }
}