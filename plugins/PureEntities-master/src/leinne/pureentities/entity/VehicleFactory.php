<?php

declare(strict_types=1);

namespace leinne\pureentities\entity;

use pocketmine\player\Player;

class VehicleFactory{

    protected static array $vehicles = [];

    public static function setVehicle(Player $player, Vehicle $vehicle) : void{
        self::$vehicles[$player->getName()] = $vehicle;
    }

    public static function getVehicle(Player $player) : ?Vehicle{
        return self::$vehicles[$player->getName()] ?? null;
    }

    public static function unsetVehicle(Player $player) : void{
        if ( self::getVehicle($player) !== null ){
            unset(self::$vehicles[$player->getName()]);
        }
    }

}