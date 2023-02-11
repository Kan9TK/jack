<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\economy;

use Cash\Cash;
use pocketmine\player\Player;

class CashProvider implements EconomyProvider{

    protected Cash $economy;

    public function __construct(){
        $this->economy = Cash::getInstance();
    }

    public static function getName(): string{
        return "Cash";
    }

    public function addMoney(Player $player, float|int $amount): void{
        $this->economy->addCash($player, $amount);
    }

    public function reduceMoney(Player $player, float|int $amount): void{
        $this->economy->reduceCash($player, $amount);
    }

    public function getMoney(Player $player): int|float{
        return $this->economy->getCash($player);
    }

    public function getUnit() : string{
        return $this->economy->getMonetaryUnit();
    }

}