<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\form;

use alvin0319\BixbyMarket\BixbyMarket;
use alvin0319\BixbyMarket\economy\EconomyProvider;
use alvin0319\BixbyMarket\inventory\SelectCategoryInventory;
use alvin0319\BixbyMarket\market\Market;
use alvin0319\BixbyMarket\shop\Shop;
use alvin0319\BixbyMarket\util\MarketBuyResult;
use alvin0319\BixbyMarket\util\MarketSellResult;
use onebone\economyapi\EconomyAPI;
use pocketmine\form\Form;
use pocketmine\player\Player;
use function count;
use function is_array;
use function is_numeric;

class MarketBuySellForm implements Form{

	public function __construct(protected Player $player, protected Shop $shop, protected Market $market){
	}

	public function jsonSerialize() : array{

	    $item = $this->market->getItem();
	    $itemName = $item->hasCustomName() ? $item->getCustomName() : $item->getName();
	    $economyProvider = $this->shop->getEconomyProvider();
	    $unit = $economyProvider->getUnit();

	    $str = " \n";
	    $str .= "§b§l• §r품목 §7> §r".$itemName;
		$str .= "\n§b§l• §r구매가 §7> §r" . ($this->market->getBuyPrice() >= 0 ? "" . EconomyAPI::getInstance()->koreanWonFormat($this->market->getBuyPrice()).$unit : "§c구매 불가");
		$str .= "\n§b§l• §r판매가 §7> §r" . ($this->market->getSellPrice() >= 0 ? "" . EconomyAPI::getInstance()->koreanWonFormat($this->market->getSellPrice()).$unit : "§c판매 불가");
		$str .= "\n\n§b§l• §r내 잔고 §7> §r" . $economyProvider->getMoney($this->player).$unit;
		$str .= "\n ";

		return [
			"type" => "custom_form",
			"title" => "{$itemName}",
			"content" => [
				[
					"type" => "label",
					"text" => $str
				],
				[
					"type" => "dropdown",
					"text" => "구매 또는 판매",
					"options" => ["구매", "판매"]
				],
				[
					"type" => "input",
					"text" => "갯수"
				],
                [
                    "type" => "toggle",
                    "text" => "상점에 머무르시겠어요?",
                    "default" => true
                ]
			]
		];
	}

	public function handleResponse(Player $player, $data) : void{
		if(!is_array($data) || count($data) !== 4){
			return;
		}
		[, $buyOrSell, $amount, $continue] = $data;

		if(!is_numeric($amount) || ($amount = (int) $amount) < 1){
			return;
		}

		$economyProvider = $this->shop->getEconomyProvider();

		if($buyOrSell === 0){
		    $unit = $economyProvider->getUnit();
		    $money_before = EconomyAPI::getInstance()->koreanWonFormat($economyProvider->getMoney($player)).$unit;
			$result = $this->market->buy($player, $amount, $economyProvider);
            $money_after = EconomyAPI::getInstance()->koreanWonFormat($economyProvider->getMoney($player)).$unit;
			if($result->equals(MarketBuyResult::SUCCESS())){
				$price = $this->market->getBuyPrice() * $amount;
				$player->sendMessage(BixbyMarket::$prefix . "성공적으로 {$this->market->getItem()->getName()} {$amount}개§r§7를 구매했습니다.");
                $player->sendMessage(BixbyMarket::$prefix . "구매 전: ".$money_before." §r§7/ 구매 후: ".$money_after);
				if ( $continue ){
				    $inv = new SelectCategoryInventory($player, $this->shop);
				    $inv->send();
                }
				return;
			}
			switch($result->name()){
				case MarketBuyResult::NOT_BUYABLE()->name():
					$player->sendMessage(BixbyMarket::$prefix . "이 아이템은 구매가 불가능해요.");
					break;
				case MarketBuyResult::NOT_ENOUGH_INV():
					$player->sendMessage(BixbyMarket::$prefix . "인벤토리를 비우고 구매해주세요.");
					break;
				case MarketBuyResult::NOT_ENOUGH_MONEY():
					$player->sendMessage(BixbyMarket::$prefix . "재화가 부족하여 구매할 수 없어요.");
					break;
				case MarketBuyResult::PLUGIN_CANCEL():
					$player->sendMessage(BixbyMarket::$prefix . "오류가 발생했어요.");
					break;
			}
		}else{
			$result = $this->market->sell($player, $amount, $economyProvider);
			if($result->equals(MarketSellResult::SUCCESS())){
				$price = $this->market->getBuyPrice() * $amount;
				$player->sendMessage(BixbyMarket::$prefix . "You sold {$this->market->getItem()->getName()} x{$amount} for \${$price}");
				return;
			}
			switch($result->name()){
				case MarketSellResult::NOT_SELLABLE()->name():
					$player->sendMessage(BixbyMarket::$prefix . "이 아이템은 판매가 불가능해요.");
					break;
				case MarketSellResult::NO_ITEM():
					$player->sendMessage(BixbyMarket::$prefix . "아이템이 부족하여 판매할 수 없어요.");
					break;
				case MarketBuyResult::PLUGIN_CANCEL():
					$player->sendMessage(BixbyMarket::$prefix . "오류가 발생했어요.");
					break;
			}
		}
	}
}