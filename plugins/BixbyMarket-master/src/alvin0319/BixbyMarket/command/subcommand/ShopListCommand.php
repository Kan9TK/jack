<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\command\subcommand;

use alvin0319\BixbyMarket\BixbyMarket;
use alvin0319\BixbyMarket\category\Category;
use alvin0319\BixbyMarket\shop\Shop;
use kang\CommandLibrary\SubCommand;
use kang\ServerUtils\ServerUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use function count;
use function implode;

final class ShopListCommand extends SubCommand {

    public function __construct(){
        parent::__construct("상점목록", "상점 목록을 확인해요.", "상점목록");
    }

    public function prepare(): void
    {
        $this->setPermission("op");
    }

    public function onExecute(CommandSender $player, string $commandLabel, array $args) : bool{
        if(!$player instanceof Player){
            return true;
        }

        $shops = array_keys(BixbyMarket::getInstance()->getShopManager()->getShops());
        ServerUtils::msg($player, "총 ".count($shops)."개의 상점이 존재해요.");
        ServerUtils::msg($player, "상점 목록: ".implode(", ", $shops));
        return true;
    }
}