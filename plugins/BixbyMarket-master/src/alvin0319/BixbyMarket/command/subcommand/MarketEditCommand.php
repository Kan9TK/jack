<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\command\subcommand;

use alvin0319\BixbyMarket\BixbyMarket;
use alvin0319\BixbyMarket\form\MarketEditForm;
use kang\CommandLibrary\SubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

final class MarketEditCommand extends SubCommand{

	public function __construct(){
		parent::__construct("가격수정", "가격을 수정해요.", "가격수정");
	}

	public function prepare(): void
    {
        $this->setPermission("op");
    }

    public function onExecute(CommandSender $player, string $commandLabel, array $args) : bool{
		if(!$player instanceof Player){
			return true;
		}
		$item = $player->getInventory()->getItemInHand();
		$market = BixbyMarket::getInstance()->getMarketManager()->getMarketByItem($item);
		if($item->isNull() || $market === null){
			$player->sendMessage(BixbyMarket::$prefix . "해당 아이템은 상점에 등록되어 있지 않아요.");
			return true;
		}
		$player->sendForm(new MarketEditForm($market));
		return true;
	}
}