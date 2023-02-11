<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\category;

use alvin0319\BixbyMarket\BixbyMarket;
use alvin0319\BixbyMarket\economy\EconomyProvider;
use alvin0319\BixbyMarket\market\Market;
use alvin0319\BixbyMarket\task\SellAllTask;
use JsonSerializable;
use pocketmine\item\Item;
use pocketmine\player\Player;

final class Category implements JsonSerializable{

	public function __construct(protected string $name, protected array $markets, protected Item $item, protected int $pages){
		foreach($markets as $position => $marketId){
			$market = BixbyMarket::getInstance()->getMarketManager()->getMarketById($marketId);
			if($market !== null){
				$this->markets[$position] = $market;
			}
		}
	}

	public function getName() : string{
		return $this->name;
	}

	/** @return Market[] */
	public function getMarkets() : array{
		return $this->markets;
	}

	public function setMarkets(array $markets) : void{
	    $this->markets = $markets;
	}

	public function getMarketByIndex(int $index) : ?Market{
		return $this->markets[$index] ?? null;
	}

	public function getIndexByMarket(Market $market) : ?int{
		foreach($this->markets as $index => $m){
			if($m->getId() === $market->getId()){
				return $index;
			}
		}
		return null;
	}

	public function setMarketIndex(int $index, ?Market $market) : void{
		if($market === null){
			if(isset($this->markets[$index])){
				unset($this->markets[$index]);
			}
		}else{
			$this->markets[$index] = $market;
		}
	}

	public function getItem() : Item{
		return clone $this->item;
	}


    public function sellAll(Player $player, EconomyProvider $economyProvider) : void{
        (new SellAllTask($this, $player, $economyProvider))->start();
    }

	public function jsonSerialize() : array{
		$res = [];
		foreach($this->markets as $index => $market){
			$res[$index] = $market->getId();
		}
		return [
			"name" => $this->name,
			"markets" => $res,
			"item" => $this->item->jsonSerialize(),
            "pages" => $this->pages
		];
	}

	public static function jsonDeserialize(array $data) : Category{
		return new Category($data["name"], $data["markets"], Item::jsonDeserialize($data["item"]), $data["pages"] ?? 0);
	}
}