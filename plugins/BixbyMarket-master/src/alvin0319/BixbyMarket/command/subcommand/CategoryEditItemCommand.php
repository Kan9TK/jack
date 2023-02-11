<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\command\subcommand;

use alvin0319\BixbyMarket\BixbyMarket;
use alvin0319\BixbyMarket\inventory\EditCategoryItemsInventory;
use kang\CommandLibrary\SubCommand;
use kang\ServerUtils\ServerUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use function count;
use function implode;

final class CategoryEditItemCommand extends SubCommand {

	public function __construct(){
		parent::__construct("품목수정", "품목을 수정해요.", "품목수정 [상점] [카테고리]");
		$this->setPermission("bixbymarket.command.edit_market");
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

        $shopName = $args[0];
		$categoryName = $args[1];

        $shop = BixbyMarket::getInstance()->getShopManager()->getShop($shopName);
        if($shop === null){
            $player->sendMessage(BixbyMarket::$prefix . "해당 이름의 상점이 존재하지 않아요.");
            return true;
        }
		$category = $shop->getCategory($categoryName);
        if($category === null){
            $player->sendMessage(BixbyMarket::$prefix . "해당 이름의 카테고리가 존재하지 않아요.");
        }

        $inv = new EditCategoryItemsInventory($player, $shop, $category);
        $inv->send();

		return true;
	}
}