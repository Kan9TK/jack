<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\command\subcommand;

use alvin0319\BixbyMarket\BixbyMarket;
use alvin0319\BixbyMarket\inventory\MarketInventory;
use kang\CommandLibrary\SubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use function count;
use function implode;

class ShopDeleteCommand extends SubCommand {

    public function __construct(){
        parent::__construct("카테고리", "카테고리를 수정해요.", "카테고리 [상점]");
    }

    public function prepare(): void
    {
        $this->setPermission("op");
    }

    public function onExecute(CommandSender $player, string $commandLabel, array $args) : bool{
        if(!$player instanceof Player){
            return true;
        }

        if ( count($args) < 1 ){
            return false;
        }

        $shopName = $args[0];

        $shop = BixbyMarket::getInstance()->getShopManager()->getShop($shopName);
        if($shop === null){
            $player->sendMessage(BixbyMarket::$prefix . "해당 이름의 상점이 존재하지 않아요.");
            return true;
        }

        BixbyMarket::getInstance()->getShopManager()->deleteShop($shopName);
        $player->sendMessage(BixbyMarket::$prefix . "성공적으로 해당 상점을 제거했어요.");
        return true;
    }
}