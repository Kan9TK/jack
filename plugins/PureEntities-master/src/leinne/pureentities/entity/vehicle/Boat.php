<?php

declare(strict_types=1);

namespace leinne\pureentities\entity\vehicle;

use leinne\pureentities\entity\Vehicle;
use leinne\pureentities\entity\VehicleFactory;
use leinne\pureentities\PureEntities;
use pocketmine\entity\animation\HurtAnimation;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\AnimateEntityPacket;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\network\mcpe\protocol\SetActorLinkPacket;
use pocketmine\network\mcpe\protocol\types\ActorEvent;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityLink;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataTypes;
use pocketmine\network\mcpe\protocol\types\entity\IntMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\LongMetadataProperty;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;

class Boat extends Vehicle{

    /** @var float */
    public $gravity = 0.0;
    /** @var float */
   // public $drag = 0.1;

    public ?Player $rider = null;

    public function getName() : string{
        return "Boat";
    }

    public function initEntity(CompoundTag $nbt) : void{
        parent::initEntity($nbt);
        $this->setMaxHealth(1);
        $this->setHealth(1);
        $this->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::STACKABLE, true);
    }

    /*public function attack(EntityDamageEvent $source) : void{
        parent::attack($source);
        if(!$source->isCancelled()){
            $pk = new ActorEventPacket();
            $pk->actorRuntimeId = $this->getId();
            $pk->eventId = ActorEvent::HURT_ANIMATION;
            //$this->getWorld()->broadcastPacketToViewers($this->location, $pk);
            //Server::getInstance()->broadcastPackets($this->getViewers(), [$pk]);
            $this->broadcastAnimation(new HurtAnimation($this), $this->getViewers());
        }
    }*/

    protected function onDeath() : void{

        $meta = 0;
        $properties = $this->getNetworkProperties()->getAll();
        $property = $properties[EntityMetadataProperties::VARIANT];
        if ( $property instanceof IntMetadataProperty ){
            $meta = $property->getValue();
        }

        $this->getWorld()->dropItem($this->location, ItemFactory::getInstance()->get(ItemIds::BOAT, $meta));
    }

    public function onUpdate(int $currentTick) : bool{
        if($this->closed){
            return false;
        }

        if($this->getHealth() < $this->getMaxHealth() && $currentTick % 10 === 0){
            $this->heal(new EntityRegainHealthEvent($this, 1, EntityRegainHealthEvent::CAUSE_REGEN));
        }

        return parent::onUpdate($currentTick);
    }

    public function canLink(Player $rider) : bool{
        return $this->rider === null;
    }

    public function link(Player $rider) : bool{
        if($this->rider === null){
            $rider->getNetworkProperties()->setgENERICFlag(EntityMetadataFlags::RIDING, true);
            $rider->getNetworkProperties()->setVector3(EntityMetadataProperties::RIDER_SEAT_POSITION, new Vector3(0, 1, 0));
            //$rider->getNetworkProperties()->setByte(EntityMetadataProperties::RIDER_ROTATION_LOCKED, 1);
            $rider->getNetworkProperties()->setFloat(EntityMetadataProperties::RIDER_MAX_ROTATION, 90);
            $rider->getNetworkProperties()->setFloat(EntityMetadataProperties::RIDER_MIN_ROTATION, -90);

            VehicleFactory::setVehicle($rider, $this);

            $pk = new SetActorLinkPacket();
            $pk->link = new EntityLink($this->getId(), $rider->getId(), EntityLink::TYPE_RIDER, true, true);
            Server::getInstance()->broadcastPackets($this->getViewers(), [$pk]);

            $this->rider = $rider;

            return true;
        }

        return false;
    }

    public function unlink(Player $rider) : bool{
        if($this->rider === $rider){
            $rider->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::RIDING, false);
            $rider->getNetworkProperties()->setVector3(EntityMetadataProperties::RIDER_SEAT_POSITION, new Vector3(0, 0, 0));
            $rider->getNetworkProperties()->setByte(EntityMetadataProperties::RIDER_ROTATION_LOCKED, 0);

            VehicleFactory::unsetVehicle($rider);

            $pk = new SetActorLinkPacket();
            $pk->link = new EntityLink($this->getId(), $rider->getId(), EntityLink::TYPE_REMOVE, true, true);
            Server::getInstance()->broadcastPackets($this->getViewers(), [$pk]);

            $this->rider = null;
            return true;
        }

        return false;
    }

    public function getRider() : ?Player{
        return $this->rider;
    }

    public function isRider(Player $rider) : bool{
        return $this->rider === $rider;
    }

    public function interact(Player $player, Item $item)
    {
        $this->link($player);
    }

    public function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(0.455, 1.4);
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::BOAT;
    }

}