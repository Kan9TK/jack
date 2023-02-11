<?php

declare(strict_types=1);

namespace alvin0319\Area\listener;

use alvin0319\Area\area\AreaProperties;
use alvin0319\Area\AreaLoader;
use alvin0319\Area\world\WorldData;
use kang\ServerUtils\ServerUtils;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;

class EventListener implements Listener{

    public function __construct(private AreaLoader $plugin){

    }

    public function onBlock(BlockBreakEvent|BlockPlaceEvent|PlayerInteractEvent|SignChangeEvent $event) : void{
        $player = $event->getPlayer();
        $block = $event->getBlock();

        $area = $this->plugin->getAreaManager()->getArea($player->getPosition(), $player->getWorld());
        if ( $area !== null ){
            if ( $area->getAreaProperties()->get(AreaProperties::PROTECT) ){
                $event->cancel();
                return;
            }
        }
        $world = $this->plugin->getWorldManager()->get($block->getPosition()->getWorld());
        if (!$player->hasPermission(DefaultPermissions::ROOT_OPERATOR) && $world->get(WorldData::PROTECT)) {
            $event->cancel();
        }
    }

    public function onEntityDamageByEntity(EntityDamageByEntityEvent $event) : void{
        $damager = $event->getDamager();
        $entity = $event->getEntity();
        if(!$damager instanceof Player || !$entity instanceof Player){
            return;
        }
        $area = $this->plugin->getAreaManager()->getArea($entity->getPosition(), $entity->getWorld());
        if ( $area !== null ){
            if ( ! $area->getAreaProperties()->get(AreaProperties::PVP) ){
                $event->cancel();
                return;
            }
        }
        $world = $this->plugin->getWorldManager()->get($entity->getWorld());
        if ( ! $world->get(WorldData::PVP)) {
            $event->cancel();
        }
    }

    public function onPlayerDeath(PlayerDeathEvent $event) : void{
        $player = $event->getPlayer();
        $area = $this->plugin->getAreaManager()->getArea($player->getPosition(), $player->getWorld());
        if ( $area !== null ){
            if ( $area->getAreaProperties()->get(AreaProperties::INVENTORY_SAVE) ){
                $event->setKeepInventory(true);
            }else{
                $event->setDrops($player->getInventory()->getContents());
            }
            return;
        }
        $world = $this->plugin->getWorldManager()->get($player->getWorld());
        if ( $world->get(WorldData::INVENTORY_SAVE)) {
            $event->setKeepInventory(true);
        }else{
            $event->setDrops($player->getInventory()->getContents());
        }
    }

    public function onItemDrop(PlayerDropItemEvent $event) : void{
        $player = $event->getPlayer();
        $world = $this->plugin->getWorldManager()->get($player->getWorld());
        if ( ! $world->get(WorldData::ITEM_DROP)) {
            ServerUtils::error($player, "이 월드에서는 아이템을 버릴 수 없어요.");
            $event->cancel();
        }
    }

}