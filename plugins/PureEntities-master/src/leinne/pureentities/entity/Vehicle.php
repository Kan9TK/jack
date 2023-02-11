<?php

declare(strict_types=1);

namespace leinne\pureentities\entity;

use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\player\Player;

abstract class Vehicle extends Entity{

    public function interact(Player $player, Item $item){
    }

    public function link(Player $rider){

    }

    public function unlink(Player $rider){

    }

    public function getRider() : ?Player{
        return $this->rider;
    }

    public function absoluteMove(Vector3 $pos, float $yaw = 0, float $pitch = 0) : void{
        $this->setPosition($pos);
        $this->setRotation($yaw, $pitch);
        $this->updateMovement();
    }

    public function handleAnimatePacket(AnimatePacket $packet) : bool{
        if($this->getRider() !== null){
            switch($packet->action){
                case AnimatePacket::ACTION_ROW_RIGHT:
                    $this->getNetworkProperties()->setFloat(EntityMetadataProperties::PADDLE_TIME_RIGHT, $packet->float);
                    return true;
                case AnimatePacket::ACTION_ROW_LEFT:
                    $this->getNetworkProperties()->setFloat(EntityMetadataProperties::PADDLE_TIME_LEFT, $packet->float);
                    return true;
            }
        }
        return false;
    }

    public function updateMotion(float $x, float $y) : void{}


}