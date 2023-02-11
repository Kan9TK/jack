<?php

declare(strict_types=1);

namespace leinne\pureentities\task;

use leinne\pureentities\data\BiomeEntityList;
use leinne\pureentities\data\Setting;
use leinne\pureentities\entity\hostile\Creeper;
use leinne\pureentities\entity\hostile\Skeleton;
use leinne\pureentities\entity\hostile\Zombie;
use leinne\pureentities\entity\LivingBase;
use leinne\pureentities\entity\neutral\IronGolem;
use leinne\pureentities\entity\neutral\Spider;
use leinne\pureentities\entity\neutral\ZombifiedPiglin;
use leinne\pureentities\entity\passive\Chicken;
use leinne\pureentities\entity\passive\Cow;
use leinne\pureentities\entity\passive\Mooshroom;
use leinne\pureentities\entity\passive\Pig;
use leinne\pureentities\entity\passive\Sheep;
use pocketmine\block\Water;
use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\world\Position;
use pocketmine\world\World;

class AutoSpawnTask extends Task{

    public function onRun() : void{

        foreach ( Server::getInstance()->getWorldManager()->getWorlds() as $world ){

            if ( ! in_array($world->getFolderName(), Setting::SPAWN_WORLDS) ) continue;

            $positions = [];
            foreach ($world->getPlayers() as $player) {

                if($this->getNearEntities($player->getPosition())>5){
                    //스폰 불가능
                    continue;
                }

                $positions[] = $this->getSaferSpawn($player->getPosition(), $world, 100);
            }

            foreach ( $positions as $position ) {

                $entityClasses = [
                    //낮
                    [Cow::class, Pig::class, Sheep::class, Chicken::class, Mooshroom::class, IronGolem::class],//, "Slime", "Wolf", "Ocelot", "Mooshroom", "Rabbit", "IronGolem", "SnowGolem"],
                    //밤
                    [Zombie::class, Creeper::class, Skeleton::class, Spider::class, ZombifiedPiglin::class]//, "Enderman", "CaveSpider", "MagmaCube", "ZombieVillager", "Ghast", "Blaze"]
                ];

                $time = $world->getTime() % World::TIME_FULL;
                if ($time < World::TIME_NIGHT || $time > World::TIME_SUNRISE) {
                    $entityClasses = $entityClasses[0]; // 낮
                } else {
                    $entityClasses = $entityClasses[1]; // 밤
                }

                $biomeId = $world->getBiomeId((int)$position->getX(), (int)$position->getZ());

                $entityList = array_unique(array_merge(
                    BiomeEntityList::BIOME_ENTITIES[$biomeId] ?? [],
                    BiomeEntityList::BIOME_HOSTILE_MOBS[$biomeId] ?? [],
                    BiomeEntityList::BIOME_ANIMALS[$biomeId] ?? []
                ));

                if (empty($entityList)) continue;

                $entityId = $entityList[array_rand($entityList)];

                if (!in_array($entityId, $entityClasses)) continue;

                $entity = new $entityId(Location::fromObject($position, $world));

                if ($entity instanceof LivingBase) {

                    $entity->spawnToAll();

                }
            }
        }
    }

    public function getSaferSpawn(Vector3 $start, World $world, int $radius) : Vector3 {
        for ($r = $radius; $r > 5; $r -= 5) {
            $x = mt_rand(-$r, $r);
            $m = sqrt(pow($r, 2) - pow($x, 2));
            $z = mt_rand((int)-$m, (int)$m);

            $vec = new Vector3($start->x + $x, $start->y, $start->z + $z);

            $chunk = $world->getOrLoadChunkAtPosition($vec);

            if ($chunk === null) {
                continue;
            }

            $safe = $world->getSafeSpawn($vec);

            if ($safe->y > 0) {
                return $safe;
            }
        }

        return $start;
    }

    public function getNearEntities(Position $pos) : int{
        $c = 0;
        foreach ( $pos->getWorld()->getEntities() as $entity ) {
            if ($entity instanceof LivingBase) {
                if ($pos->distance($entity->getPosition()) < 150) { // 150 거리 안의 엔티티 탐색
                    $c++;
                    /*if ($c > 5) { // 5마리 이상일 경우 제거
                        $entity->close();
                    }*/
                }
            }
        }
        return $c;
    }

}