<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\market;

use alvin0319\BixbyMarket\BixbyMarket;
use alvin0319\BixbyMarket\category\Category;
use alvin0319\BixbyMarket\economy\EconomyProvider;
use alvin0319\BixbyMarket\event\ItemBuyEvent;
use alvin0319\BixbyMarket\event\ItemSellEvent;
use alvin0319\BixbyMarket\util\MarketBuyResult;
use alvin0319\BixbyMarket\util\MarketSellResult;
use JsonSerializable;
use onebone\economyapi\EconomyAPI;
use pocketmine\item\Item;
use pocketmine\player\Player;

final class Market implements JsonSerializable{

	public function __construct(private int $id, private Item $item, private int $buyPrice, private int $sellPrice){
	}

	public function getId() : int{
		return $this->id;
	}

	public function getItem() : Item{
		return clone $this->item;
	}

	public function getBuyPrice() : int{
		return $this->buyPrice;
	}

	public function getSellPrice() : int{
		return $this->sellPrice;
	}

	public function setBuyPrice(int $buyPrice) : void{
		$this->buyPrice = $buyPrice;
	}

	public function setSellPrice(int $sellPrice) : void{
		$this->sellPrice = $sellPrice;
	}

	public function buy(Player $player, int $buyCount, EconomyProvider $economyProvider) : MarketBuyResult{
		if($this->buyPrice < 0){
			return MarketBuyResult::NOT_BUYABLE();
		}
		$price = $this->buyPrice * $buyCount;
		if($economyProvider->getMoney($player) < $price){
			return MarketBuyResult::NOT_ENOUGH_MONEY();
		}
		$item = $this->getItem()
			->setCount($buyCount);
		if(!$player->getInventory()->canAddItem($item)){
			return MarketBuyResult::NOT_ENOUGH_INV();
		}

		$ev = new ItemBuyEvent($player, $this, $buyCount);
		$ev->call();

		if($ev->isCancelled()){
			return MarketBuyResult::PLUGIN_CANCEL();
		}

		$economyProvider->reduceMoney($player, $price);
		$player->getInventory()->addItem($item);
		return MarketBuyResult::SUCCESS();
	}

	public function sell(Player $player, int $sellCount, EconomyProvider $economyProvider) : MarketSellResult{
		if($this->sellPrice < 0){
			return MarketSellResult::NOT_SELLABLE();
		}
		$item = $this->getItem()
			->setCount($sellCount);
		if(!$player->getInventory()->contains($item)){
			return MarketSellResult::NO_ITEM();
		}

		$ev = new ItemSellEvent($player, $this, $sellCount);
		$ev->call();

		if($ev->isCancelled()){
			return MarketSellResult::PLUGIN_CANCEL();
		}

		$player->getInventory()->removeItem($item);
        $economyProvider->addMoney($player, $this->sellPrice * $sellCount);
		return MarketSellResult::SUCCESS();
	}

	public function jsonSerialize() : array{
		return [
			"id" => $this->id,
			"item" => $this->item->jsonSerialize(),
			"buyPrice" => $this->buyPrice,
			"sellPrice" => $this->sellPrice
		];
	}

	public static function jsonDeserialize(array $data) : Market{
		return new Market(
			$data["id"],
			Item::jsonDeserialize($data["item"]),
			$data["buyPrice"],
			$data["sellPrice"]
		);
	}
}