<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\entity;

use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Human;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\world\World;

class EntityManager{

    public function __construct(){
        EntityFactory::getInstance()->register(ShopEntity::class, function(World $world, CompoundTag $nbt) : ShopEntity{
            return new ShopEntity(EntityDataHelper::parseLocation($nbt, $world), Human::parseSkinNBT($nbt), $nbt);
        }, ['Human']);
    }

    public function createEntity(Player $player, string $npcName, string $shopName) : void{

        $nbt = CompoundTag::create()->setString("shop",$shopName);

        $entity = new ShopEntity($player->getLocation(), $player->getSkin(), $nbt);


        $entity->setNameTag($npcName);
        $entity->setNameTagVisible(true);
        $entity->setNameTagAlwaysVisible(true);
        $entity->spawnToAll();
    }

}