<?php

declare(strict_types=1);

namespace leinne\pureentities;

use leinne\pureentities\entity\neutral\IronGolem;
use leinne\pureentities\entity\neutral\ZombifiedPiglin;
use leinne\pureentities\entity\neutral\Spider;
use leinne\pureentities\entity\passive\Chicken;
use leinne\pureentities\entity\passive\Cow;
use leinne\pureentities\entity\passive\Mooshroom;
use leinne\pureentities\entity\passive\Pig;
use leinne\pureentities\entity\passive\Sheep;
use leinne\pureentities\entity\hostile\Creeper;
use leinne\pureentities\entity\hostile\Skeleton;
use leinne\pureentities\entity\hostile\Zombie;
use leinne\pureentities\entity\passive\SnowGolem;
use leinne\pureentities\entity\vehicle\Boat as BoatEntity;
use leinne\pureentities\item\Boat as BoatItem;
use leinne\pureentities\listener\EventListener;
use leinne\pureentities\task\AutoSpawnTask;
use pocketmine\block\utils\TreeType;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Location;
use pocketmine\event\Listener;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\SpawnEgg;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\World;

class PureEntities extends PluginBase implements Listener{

    protected static PureEntities $instance;

    public static function getInstance() : PureEntities{
        return self::$instance;
    }

    public function onLoad() : void{
        self::$instance = $this;
    }

    public function onEnable() : void{
        $entityFactory = EntityFactory::getInstance();
        /** Register hostile */
        $entityFactory->register(Creeper::class, function(World $world, CompoundTag $nbt) : Creeper{
            return new Creeper(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ["Creeper", "minecraft:creeper"]);
        $entityFactory->register(Skeleton::class, function(World $world, CompoundTag $nbt) : Skeleton{
            return new Skeleton(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ["Skeleton", "minecraft:skeleton"]);
        $entityFactory->register(Zombie::class, function(World $world, CompoundTag $nbt) : Zombie{
            return new Zombie(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ["Zombie", "minecraft:zombie"]);

        /** Register neutral */
        $entityFactory->register(IronGolem::class, function(World $world, CompoundTag $nbt) : IronGolem{
            return new IronGolem(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ["IronGolem", "minecraft:iron_golem"]);
        $entityFactory->register(ZombifiedPiglin::class, function(World $world, CompoundTag $nbt) : ZombifiedPiglin{
            return new ZombifiedPiglin(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ["ZombiePigman", "minecraft:zombie_pigman"]);
        $entityFactory->register(Spider::class, function(World $world, CompoundTag $nbt) : Spider{
            return new Spider(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ["Spider", "minecraft:spider"]);

        /** Register passive */
        $entityFactory->register(Chicken::class, function(World $world, CompoundTag $nbt) : Chicken{
            return new Chicken(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ["Chicken", "minecraft:chicken"]);
        $entityFactory->register(Cow::class, function(World $world, CompoundTag $nbt) : Cow{
            return new Cow(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ["Cow", "minecraft:cow"]);
        $entityFactory->register(Mooshroom::class, function(World $world, CompoundTag $nbt) : Mooshroom{
            return new Mooshroom(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ["Mooshroom", "minecraft:mooshroom"]);
        $entityFactory->register(Pig::class, function(World $world, CompoundTag $nbt) : Pig{
            return new Pig(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ["Pig", "minecraft:pig"]);
        $entityFactory->register(Sheep::class, function(World $world, CompoundTag $nbt) : Sheep{
            return new Sheep(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ["Sheep", "minecraft:sheep"]);
        $entityFactory->register(SnowGolem::class, function(World $world, CompoundTag $nbt) : SnowGolem{
            return new SnowGolem(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ["SnowGolem", "minecraft:snow_golem"]);

        //BlockFactory::register(new block\MonsterSpawner(new BlockIdentifier(BlockLegacyIds::MOB_SPAWNER, 0, null, tile\MonsterSpawner::class), "Monster Spawner"), true);

        $itemFactory = ItemFactory::getInstance();
        /** Register hostile */
        $itemFactory->register(new class(new ItemIdentifier(ItemIds::SPAWN_EGG, EntityLegacyIds::CREEPER), "Creeper Spawn Egg") extends SpawnEgg{
            protected function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch) : Entity{
                return new Creeper(Location::fromObject($pos, $world, $yaw, $pitch));
            }
        }, true);
        $itemFactory->register(new class(new ItemIdentifier(ItemIds::SPAWN_EGG, EntityLegacyIds::SKELETON), "Skeleton Spawn Egg") extends SpawnEgg{
            protected function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch) : Entity{
                return new Skeleton(Location::fromObject($pos, $world, $yaw, $pitch));
            }
        }, true);
        $itemFactory->register(new class(new ItemIdentifier(ItemIds::SPAWN_EGG, EntityLegacyIds::ZOMBIE), "Zombie Spawn Egg") extends SpawnEgg{
            protected function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch) : Entity{
                return new Zombie(Location::fromObject($pos, $world, $yaw, $pitch));
            }
        }, true);

        /** Register neutral */
        $itemFactory->register(new class(new ItemIdentifier(ItemIds::SPAWN_EGG, EntityLegacyIds::IRON_GOLEM), "IronGolem Spawn Egg") extends SpawnEgg{
            protected function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch) : Entity{
                return new IronGolem(Location::fromObject($pos, $world, $yaw, $pitch));
            }
        }, true);
        $itemFactory->register(new class(new ItemIdentifier(ItemIds::SPAWN_EGG, EntityLegacyIds::ZOMBIE_PIGMAN), "ZombifiedPiglin Spawn Egg") extends SpawnEgg{
            protected function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch) : Entity{
                return new ZombifiedPiglin(Location::fromObject($pos, $world, $yaw, $pitch));
            }
        }, true);
        $itemFactory->register(new class(new ItemIdentifier(ItemIds::SPAWN_EGG, EntityLegacyIds::SPIDER), "Spider Spawn Egg") extends SpawnEgg{
            protected function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch) : Entity{
                return new Spider(Location::fromObject($pos, $world, $yaw, $pitch));
            }
        }, true);

        /** Register passive */
        $itemFactory->register(new class(new ItemIdentifier(ItemIds::SPAWN_EGG, EntityLegacyIds::CHICKEN), "Chicken Spawn Egg") extends SpawnEgg{
            protected function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch) : Entity{
                return new Chicken(Location::fromObject($pos, $world, $yaw, $pitch));
            }
        }, true);
        $itemFactory->register(new class(new ItemIdentifier(ItemIds::SPAWN_EGG, EntityLegacyIds::COW), "Cow Spawn Egg") extends SpawnEgg{
            protected function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch) : Entity{
                return new Cow(Location::fromObject($pos, $world, $yaw, $pitch));
            }
        }, true);
        $itemFactory->register(new class(new ItemIdentifier(ItemIds::SPAWN_EGG, EntityLegacyIds::MOOSHROOM), "Mooshroom Spawn Egg") extends SpawnEgg{
            protected function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch) : Entity{
                return new Mooshroom(Location::fromObject($pos, $world, $yaw, $pitch));
            }
        }, true);
        $itemFactory->register(new class(new ItemIdentifier(ItemIds::SPAWN_EGG, EntityLegacyIds::PIG), "Pig Spawn Egg") extends SpawnEgg{
            protected function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch) : Entity{
                return new Pig(Location::fromObject($pos, $world, $yaw, $pitch));
            }
        }, true);
        $itemFactory->register(new class(new ItemIdentifier(ItemIds::SPAWN_EGG, EntityLegacyIds::SHEEP), "Sheep Spawn Egg") extends SpawnEgg{
            protected function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch) : Entity{
                return new Sheep(Location::fromObject($pos, $world, $yaw, $pitch));
            }
        }, true);
        $itemFactory->register(new class(new ItemIdentifier(ItemIds::SPAWN_EGG, EntityLegacyIds::SNOW_GOLEM), "SnowGolem Spawn Egg") extends SpawnEgg{
            protected function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch) : Entity{
                return new SnowGolem(Location::fromObject($pos, $world, $yaw, $pitch));
            }
        }, true);

        /** Register item */
        foreach(TreeType::getAll() as $type){
            $itemFactory->register(new BoatItem(new ItemIdentifier(ItemIds::BOAT, $type->getMagicNumber()), $type->getDisplayName() . " Boat", $type), true);
        }

        /** Register vehicle */
        $entityFactory->register(BoatEntity::class, function(World $world, CompoundTag $nbt) : BoatEntity{
            return new BoatEntity(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ["Boat", "minecraft:boat"], EntityLegacyIds::BOAT);


        $this->saveDefaultConfig();
        $data = $this->getConfig()->getAll();

        $spawnable = $data["autospawn"] ?? [];
        if($spawnable["enable"] ?? false){
            $this->getScheduler()->scheduleRepeatingTask(new AutoSpawnTask(), (int) ($spawnable["tick"] ?? 20*10));
        }

        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
    }

    //TODO: check golem block shape
    /*private function canSpawnGolem(Position $pos, int $id) : bool{
        $resultShape = [];
        for($x = -1; $x < 2; ++$x){
            for($y = -1; $y > -3; --$y){
                $resultShape[$x + 1][$y + 2] = $pos->world->getBlock($pos->add($x, $y, 0))->getId() === $id ? "O" : "X";
            }
        }
        return $resultShape == [["O", "X"], ["O", "O"], ["O", "X"]];
    }*/

    //TODO: SilverFish
    /*public function BlockBreakEvent(BlockBreakEvent $ev){
        if($ev->isCancelled()){
            return;
        }

        $block = $ev->getBlock();
        if(
            (
                $block->getId() === BlockLegacyIds::STONE
                or $block->getId() === BlockLegacyIds::STONE_WALL
                or $block->getId() === BlockLegacyIds::STONE_BRICK
                or $block->getId() === BlockLegacyIds::STONE_BRICK_STAIRS
            ) && ($block->level->getBlockLightAt((int) $block->x, (int) $block->y, (int) $block->z) < 12 and mt_rand(1, 5) < 2)
        ){
            $entity = PureEntities::create("Silverfish", $block);
            if($entity !== \null){
                $entity->spawnToAll();
            }
        }
    }*/

}