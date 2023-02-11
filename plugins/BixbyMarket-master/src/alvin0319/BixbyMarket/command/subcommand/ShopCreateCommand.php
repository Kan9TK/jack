<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\command\subcommand;

use alvin0319\BixbyMarket\BixbyMarket;
use alvin0319\BixbyMarket\category\Category;
use alvin0319\BixbyMarket\economy\CashProvider;
use alvin0319\BixbyMarket\economy\EconomySProvider;
use alvin0319\BixbyMarket\shop\Shop;
use kang\CommandLibrary\SubCommand;
use kang\ServerUtils\ServerUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use function count;
use function implode;

final class ShopCreateCommand extends SubCommand {

    public function __construct(){
        parent::__construct("상점추가", "상점을 추가해요.", "상점추가 [상점] [이코노미]");
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

        $economyProviderManager = BixbyMarket::getInstance()->getEconomyProviderManager();
        $economyProviders = array_keys($economyProviderManager->getProviders());

        if( ($provider = $economyProviderManager->getProvider($args[1])) == null){
            ServerUtils::error($player, "사용 가능한 이코노미 종류: ".implode(", ",$economyProviders));
            return false;
        }

        $shopName = $args[0];
        $economyProvider = $provider;

        if(BixbyMarket::getInstance()->getShopManager()->getShop($shopName) !== null){
            $player->sendMessage(BixbyMarket::$prefix . "해당 이름의 상점이 이미 존재해요.");
            return true;
        }

        BixbyMarket::getInstance()->getShopManager()->addShop(new Shop($shopName, $economyProvider, []));
        $player->sendMessage(BixbyMarket::$prefix . "성공적으로 추가했어요.");
        return true;
    }
}