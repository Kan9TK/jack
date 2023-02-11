<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\command\subcommand;

use alvin0319\BixbyMarket\BixbyMarket;
use alvin0319\BixbyMarket\category\Category;
use kang\CommandLibrary\SubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use function count;
use function implode;

final class CategoryCreateCommand extends SubCommand {

	public function __construct(){
		parent::__construct("카테고리추가", "카테고리를 추가해요.", "카테고리추가 [상점] [카테고리]");
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

        if( ($shop = BixbyMarket::getInstance()->getShopManager()->getShop($shopName)) == null){
            $player->sendMessage(BixbyMarket::$prefix . "해당 이름의 상점이 존재하지 않아요.");
            return true;
        }
		
		if($shop->getCategory($categoryName) !== null){
			$player->sendMessage(BixbyMarket::$prefix . "해당 이름의 카테고리가 이미 존재해요.");
            return true;
		}

		$item = $player->getInventory()->getItemInHand();
		if($item->isNull()){
			$player->sendMessage(BixbyMarket::$prefix . "공기를 제외한 아이템을 들고 시도해주세요.");
            return true;
		}

		$shop->addCategory($shop->getAvailableIndex(), new Category($categoryName, [], $item->setCount(1), 0));
		$player->sendMessage(BixbyMarket::$prefix . "성공적으로 추가했어요.");
		return true;
	}
}