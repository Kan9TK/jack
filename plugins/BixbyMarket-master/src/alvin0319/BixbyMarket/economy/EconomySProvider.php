<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\economy;

use onebone\economyapi\EconomyAPI;
use pocketmine\player\Player;

class EconomySProvider implements EconomyProvider{

    protected EconomyAPI $economy;

    public function __construct(){
        $this->economy = EconomyAPI::getInstance();
    }

    public static function getName(): string{
        return "EconomyAPI";
    }

    public function addMoney(Player $player, float|int $amount): void{
        $this->economy->addMoney($player, $amount);
    }

    public function reduceMoney(Player $player, float|int $amount): void{
        $this->economy->reduceMoney($player, $amount);
    }

    public function getMoney(Player $player): int|float{
        return $this->economy->myMoney($player);
    }

    public function getUnit() : string{
        return $this->economy->getMonetaryUnit();
    }

}