<?php

declare(strict_types=1);

namespace leinne\pureentities\data;

use leinne\pureentities\entity\hostile\Creeper;
use leinne\pureentities\entity\hostile\Skeleton;
use leinne\pureentities\entity\hostile\Zombie;
use leinne\pureentities\entity\neutral\Spider;
use leinne\pureentities\entity\passive\Chicken;
use leinne\pureentities\entity\passive\Cow;
use leinne\pureentities\entity\passive\Pig;
use leinne\pureentities\entity\passive\Sheep;
use pocketmine\data\bedrock\BiomeIds as Biome;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class BiomeEntityList
{

    /** @var int[][] */
    public const BIOME_ENTITIES = [
        Biome::OCEAN => [
            EntityIds::SQUID,
            EntityIds::DROWNED
            // TODO: water mobs
        ],
        Biome::PLAINS => [
            Zombie::class,
            Skeleton::class,
            Creeper::class,
            Spider::class,
            EntityIds::WITCH
        ],
        Biome::DESERT => [
            Zombie::class,
            Skeleton::class,
            Creeper::class,
            Spider::class,
            EntityIds::WITCH
        ],
        /*Biome::MOUNTAINS => [
            ZOMBIE,
            SKELETON,
            CREEPER,
            SPIDER,
            WITCH
        ],*/
        Biome::FOREST => [
            Zombie::class,
            Skeleton::class,
            Creeper::class,
            Spider::class,
           EntityIds:: WITCH
        ],
        Biome::TAIGA => [
            Zombie::class,
            Skeleton::class,
            Creeper::class,
            Spider::class,
            EntityIds::WITCH
        ],
        Biome::SWAMPLAND => [
            Zombie::class,
            Skeleton::class,
            Creeper::class,
            Spider::class,
            EntityIds::WITCH
        ],
        Biome::RIVER => [
            EntityIds::SQUID,
            EntityIds::DROWNED
            // TODO: water mobs
        ],
        Biome::HELL => [
            EntityIds::ZOMBIE_PIGMAN,
            EntityIds::GHAST,
            EntityIds::MAGMA_CUBE
        ],
        Biome::ICE_PLAINS => [
            Zombie::class,
            EntityIds::STRAY,
            Creeper::class,
            Spider::class,
            EntityIds::WITCH
        ],
        /*Biome::SMALL_MOUNTAINS => [
            ZOMBIE,
            SKELETON,
            CREEPER,
            SPIDER,
            WITCH
        ],*/
        Biome::BIRCH_FOREST => [
            Zombie::class,
            Skeleton::class,
            Creeper::class,
            Spider::class,
            EntityIds::WITCH
        ]
    ];
    public const BIOME_HOSTILE_MOBS = [
        Biome::OCEAN => [
            EntityIds::DROWNED
            // TODO: water mobs
        ],
        Biome::PLAINS => [
            Zombie::class,
            Skeleton::class,
            Creeper::class,
            Spider::class,
            EntityIds::WITCH
        ],
        Biome::DESERT => [
            Zombie::class,
            Skeleton::class,
            Creeper::class,
            Spider::class,
            EntityIds::WITCH
        ],
        /*Biome::MOUNTAINS => [
            ZOMBIE,
            SKELETON,
            CREEPER,
            SPIDER,
            WITCH
        ],*/
        Biome::FOREST => [
            Zombie::class,
            Skeleton::class,
            Creeper::class,
            Spider::class,
            EntityIds::WITCH
        ],
        Biome::TAIGA => [
            Zombie::class,
            Skeleton::class,
            Creeper::class,
            Spider::class,
            EntityIds::WITCH
        ],
        Biome::SWAMPLAND => [
            Zombie::class,
            Skeleton::class,
            Creeper::class,
            Spider::class,
            EntityIds::SLIME,
            EntityIds::WITCH
        ],
        Biome::RIVER => [
            EntityIds::DROWNED
            // TODO: water mobs
        ],
        Biome::HELL => [
            EntityIds::ZOMBIE_PIGMAN,
            EntityIds::GHAST,
            EntityIds::MAGMA_CUBE
        ],
        Biome::ICE_PLAINS => [
            Zombie::class,
            Skeleton::class,
            Creeper::class,
            Spider::class,
            EntityIds::WITCH
        ],
        /*Biome::SMALL_MOUNTAINS => [
            ZOMBIE,
            SKELETON,
            CREEPER,
            SPIDER,
            WITCH
        ],*/
        Biome::BIRCH_FOREST => [
            Zombie::class,
            Skeleton::class,
            Creeper::class,
            Spider::class,
            EntityIds::WITCH
        ]
    ];
    public const BIOME_ANIMALS = [
        Biome::OCEAN => [
            EntityIds::SQUID,
            EntityIds::DOLPHIN
            // TODO: water mobs
        ],
        Biome::PLAINS => [
            Cow::class,
            Pig::class,
            Sheep::class,
            Chicken::class,
            EntityIds::HORSE,
            EntityIds::DONKEY,
            EntityIds::RABBIT
        ],
        Biome::DESERT => [
            Cow::class,
            Pig::class,
            Sheep::class,
            Chicken::class
        ],
        /*Biome::MOUNTAINS => [
            COW,
            PIG,
            SHEEP,
            CHICKEN,
            Llama
        ],*/
        Biome::FOREST => [
            Cow::class,
            Pig::class,
            Sheep::class,
            Chicken::class
        ],
        Biome::TAIGA => [
            Cow::class,
            Pig::class,
            Sheep::class,
            Chicken::class
        ],
        Biome::SWAMPLAND => [
            Cow::class,
            Pig::class,
            Sheep::class,
            Chicken::class
        ],
        Biome::RIVER => [
            EntityIds::SQUID
            // TODO: fish
        ],
        Biome::HELL => [
            // none spawn
        ],
        Biome::ICE_PLAINS => [
            Cow::class,
            Pig::class,
            Sheep::class,
            Chicken::class,
            EntityIds::POLAR_BEAR
        ],
        /*Biome::SMALL_MOUNTAINS => [
            COW,
            PIG,
            SHEEP,
            CHICKEN
        ],*/
        Biome::BIRCH_FOREST => [
            Cow::class,
            Pig::class,
            Sheep::class,
            Chicken::class
        ]
    ];

}
