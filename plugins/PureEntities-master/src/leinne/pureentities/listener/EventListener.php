<?php

declare(strict_types=1);

namespace leinne\pureentities\listener;

use leinne\pureentities\entity\LivingBase;
use leinne\pureentities\entity\neutral\IronGolem;
use leinne\pureentities\entity\passive\SnowGolem;
use leinne\pureentities\entity\Vehicle;
use leinne\pureentities\entity\VehicleFactory;
use leinne\pureentities\event\EntityInteractByPlayerEvent;

use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Location;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\math\Facing;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\network\mcpe\protocol\InteractPacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\MoveActorAbsolutePacket;
use pocketmine\network\mcpe\protocol\PlayerInputPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\player\Player;

class EventListener implements Listener{

    public function onPlayerQuitEvent(PlayerQuitEvent $event) : void{
        $player = $event->getPlayer();
        if( ($vehicle = VehicleFactory::getVehicle($player)) !== null){
            $vehicle->unlink($player);
        }
    }

    public function onEntityDeathEvent(EntityDeathEvent $event) : void{
        $player = $event->getEntity();
        if ( $player instanceof Player ) {
            if (($vehicle = VehicleFactory::getVehicle($player)) !== null) {
                $vehicle->unlink($player);
            }
        }
    }

    public function onPlayerTeleportEvent(EntityTeleportEvent $event) : void{
        $player = $event->getEntity();
        if ( $player instanceof Player ) {
            if (($vehicle = VehicleFactory::getVehicle($player)) !== null) {
                $vehicle->unlink($player);
            }
        }
    }

    /** @priority HIGHEST */
    public function onDataPacketEvent(DataPacketReceiveEvent $event) : void{
        $packet = $event->getPacket();
        if($packet instanceof InteractPacket && $packet->action === InteractPacket::ACTION_LEAVE_VEHICLE){
            var_dump("12");
            $event->cancel();
            $player = $event->getOrigin()->getPlayer();
            $entity = $player->getWorld()->getEntity($packet->targetActorRuntimeId);
            if($entity instanceof Vehicle && !$entity->isClosed()){
                $entity->unlink($player);
            }
        }elseif(
            $packet instanceof InventoryTransactionPacket &&
            $packet->trData instanceof UseItemOnEntityTransactionData &&
            $packet->trData->getActionType() === UseItemOnEntityTransactionData::ACTION_INTERACT
        ){
            $player = $event->getOrigin()->getPlayer();
            $entity = $player->getWorld()->getEntity($packet->trData->getActorRuntimeId());
            if(($entity instanceof LivingBase || $entity instanceof Vehicle) && !$entity->isClosed()){
                $event->cancel();
                $item = $player->getInventory()->getItemInHand();
                $oldItem = clone $item;
                $ev = new EntityInteractByPlayerEvent($entity, $player, $item);
                $ev->call();

                if(!$ev->isCancelled() && $entity->interact($player, $item)){
                    if(
                        $player->hasFiniteResources() &&
                        !$item->equalsExact($oldItem) &&
                        $oldItem->equalsExact($player->getInventory()->getItemInHand())
                    ){
                        $player->getInventory()->setItemInHand($item);
                    }
                }
            }
        }elseif($packet instanceof MoveActorAbsolutePacket){
            $player = $event->getOrigin()->getPlayer();
            $entity = $player->getWorld()->getEntity($packet->actorRuntimeId);
            if($entity instanceof Vehicle && !$entity->isClosed() && $entity->getRider() === $player){
                $event->cancel();
                //[xRot, yRot, zRot] = [pitch, headYaw, yaw]
                $entity->absoluteMove($packet->position, $packet->headYaw, $packet->pitch);
            }
        }elseif($packet instanceof AnimatePacket){
            $player = $event->getOrigin()->getPlayer();
            $vehicle = VehicleFactory::getVehicle($player);
            if($vehicle !== null && !$vehicle->isClosed() && $vehicle->handleAnimatePacket($packet)){
                $event->cancel();
            }
        }elseif($packet instanceof PlayerInputPacket){
            $player = $event->getOrigin()->getPlayer();
            $vehicle = VehicleFactory::getVehicle($player);
            if($vehicle !== null && !$vehicle->isClosed() && $vehicle->getRider() === $player){
                $event->cancel();
                $vehicle->updateMotion($packet->motionX, $packet->motionY);
            }
        }
    }

    public function onInteractEvent(PlayerInteractEvent $ev) : void{
        //TODO: MonsterSpawner 기능 준비
        /*if($ev->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK){
            return;
        }

        $item = $ev->getItem();
        $block = $ev->getBlock();
        if($item->getId() === ItemIds::SPAWN_EGG && $block->getId() === ItemIds::MONSTER_SPAWNER){
            $ev->cancel();

            $tile = $block->getPos()->getWorld()->getTile($block->getPos());
            if($tile instanceof tile\MonsterSpawner){
                $tile->setSpawnEntityType($item->getMeta());
            }else{
                if($tile !== null){
                    $tile->close();
                }

                $tile = TileFactory::create("MobSpawner", $block->getPos()->getWorld(), $block->getPos());
                $tile->readSaveData(CompoundTag::create()->setInt("EntityId", $item->getMeta()));
                $tile->getPos()->getWorld()->addTile($tile);
            }
        }*/
    }

    /**
     * @priority MONITOR
     *
     * @param BlockPlaceEvent $ev
     */
    public function onBlockPlaceEvent(BlockPlaceEvent $ev) : void{
        $item = $ev->getItem();
        $block = $ev->getBlock();
        $player = $ev->getPlayer();
        $bid = $block->getId();
        if($bid === BlockLegacyIds::JACK_O_LANTERN || $bid === BlockLegacyIds::PUMPKIN || $bid === BlockLegacyIds::CARVED_PUMPKIN){
            if(
                $block->getSide(Facing::DOWN)->getId() === BlockLegacyIds::SNOW_BLOCK
                && $block->getSide(Facing::DOWN, 2)->getId() === BlockLegacyIds::SNOW_BLOCK
            ){
                $ev->cancel();

                $pos = $block->getPosition()->asVector3();
                $air = VanillaBlocks::AIR();
                for($y = 0; $y < 2; ++$y){
                    --$pos->y;
                    $block->getPosition()->getWorld()->setBlock($pos, $air);
                }

                $entity = new SnowGolem(Location::fromObject($block->getPosition()->add(0.5, -2, 0.5), $block->getPosition()->getWorld()));
                $entity->spawnToAll();

                if($player->hasFiniteResources()){
                    $item->pop();
                    $player->getInventory()->setItemInHand($item);
                }
            }elseif(
                ($down = $block->getSide(Facing::DOWN))->getId() === BlockLegacyIds::IRON_BLOCK
                && $block->getSide(Facing::DOWN, 2)->getId() === BlockLegacyIds::IRON_BLOCK
            ){
                if(($first = $down->getSide(Facing::EAST))->getId() === BlockLegacyIds::IRON_BLOCK){
                    $second = $down->getSide(Facing::WEST);
                }

                if(!isset($second) && ($first = $down->getSide(Facing::NORTH))->getId() === BlockLegacyIds::IRON_BLOCK){
                    $second = $down->getSide(Facing::SOUTH);
                }

                if(!isset($second) || $second->getId() !== BlockLegacyIds::IRON_BLOCK){
                    return;
                }

                $ev->cancel();
                $entity = new IronGolem(Location::fromObject($pos = $block->getPosition()->add(0.5, -2, 0.5), $block->getPosition()->getWorld()), CompoundTag::create()->setByte("PlayerCreated", 1));
                $entity->spawnToAll();

                $down->getPosition()->getWorld()->setBlock($pos, $air = VanillaBlocks::AIR());
                $down->getPosition()->getWorld()->setBlock($first->getPosition(), $air);
                $down->getPosition()->getWorld()->setBlock($second->getPosition(), $air);
                $down->getPosition()->getWorld()->setBlock($block->getPosition()->add(0, -1, 0), $air);

                if($player->hasFiniteResources()){
                    $item->pop();
                    $player->getInventory()->setItemInHand($item);
                }
            }
        }
    }

}