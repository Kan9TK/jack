<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\economy;

use pocketmine\player\Player;

interface EconomyProvider{

    public static function getName() : string;

    public function addMoney(Player $player, int|float $amount): void;

    public function reduceMoney(Player $player, int|float $amount): void;

    public function getMoney(Player $player): int|float;

    public function getUnit() : string;

}