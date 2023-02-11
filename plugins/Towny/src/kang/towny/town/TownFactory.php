<?php

declare(strict_types=1);

namespace kang\towny\town;

use kang\towny\Towny;
use pocketmine\player\Player;

class TownFactory{

    public static function createTown(Player $player, string $townName) : Town{
        $playerPos = $player->getPosition();
        return new Town(
            $townName,
            intval($playerPos->x),
            intval($playerPos->z),
            $playerPos,
            0,
            $player->getName(),
            [],
            [$player->getName()=>true],
            0,
            0,
            [],
            []
        );
    }

    public static function deleteTown(string $playerName) : void{
        $townManager = Towny::getInstance()->getTownManager();
        if ( ($town = $townManager->getTownByName($playerName)) !== null ){
            if ( $town->isOwner($playerName) ){
                $townManager->deleteTown($town);
            }
        }
    }

}