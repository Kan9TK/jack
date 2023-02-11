<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\event;

use alvin0319\BixbyMarket\market\Market;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

final class ItemBuyEvent extends PlayerEvent implements Cancellable{

    use CancellableTrait;

	protected Market $market;

	protected int $buyAmount;

	public function __construct(Player $player, Market $market, int $buyAmount){
		$this->player = $player;
		$this->market = $market;
		$this->buyAmount = $buyAmount;
	}

	public function getMarket() : Market{
		return $this->market;
	}

	public function getBuyAmount() : int{
		return $this->buyAmount;
	}

}