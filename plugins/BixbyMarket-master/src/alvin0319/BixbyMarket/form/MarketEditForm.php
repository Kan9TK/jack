<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\form;

use alvin0319\BixbyMarket\BixbyMarket;
use alvin0319\BixbyMarket\market\Market;
use pocketmine\form\Form;
use pocketmine\player\Player;
use function count;
use function is_array;
use function is_numeric;

final class MarketEditForm implements Form{

	protected Market $market;

	public function __construct(Market $market){
		$this->market = $market;
	}

	public function jsonSerialize() : array{

        $item = $this->market->getItem();
        $itemName = $item->hasCustomName() ? $item->getCustomName() : $item->getName();

		return [
			"type" => "custom_form",
			"title" => "가격 수정",
			"content" => [
			    [
			        "type" => "label",
                    "text" => " \n§b§l• §r품목: ".$itemName."\n "
                ],
				[
					"type" => "input",
					"text" => "구매가: " . $this->market->getBuyPrice() . "\n갯수가 -1 일 경우 구매 불가"
				],
				[
					"type" => "input",
					"text" => "판매가: " . $this->market->getSellPrice() . "\n갯수가 -1 일 경우 판매 불가"
				]
			]
		];
	}

	public function handleResponse(Player $player, $data) : void{
		if(!is_array($data) || count($data) !== 3){
			return;
		}
		[$label, $buyPrice, $sellPrice] = $data;

		if(!is_numeric($buyPrice) || !is_numeric($sellPrice)){
			$player->sendMessage(BixbyMarket::$prefix . "숫자로 입력해주세요.");
			return;
		}
		$this->market->setBuyPrice((int) $buyPrice);
		$this->market->setSellPrice((int) $sellPrice);
		$player->sendMessage(BixbyMarket::$prefix . "성공적으로 해당 아이템의 구매가를 ".$buyPrice."원으로, 판매가를 ".$sellPrice."원으로 수정했어요.");
	}
}