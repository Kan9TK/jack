<?php

declare(strict_types=1);

namespace kang\wild\listener;

use kang\ServerUtils\ServerUtils;
use kang\wild\Wild;
use onebone\economyapi\EconomyAPI;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\player\Player;

class EventListener implements Listener{

    public function __construct(private Wild $plugin){
    }

    public function onBlock(BlockBreakEvent|BlockPlaceEvent|PlayerInteractEvent|SignChangeEvent $event) : void{
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $blockPos = $block->getPosition();

        if ( $blockPos->getX() <= Wild::minX or $blockPos->getX() >= Wild::maxX or $blockPos->getZ() <= Wild::minZ or $blockPos->getZ() >= Wild::maxZ ){
            ServerUtils::error($player, "일반 야생의 제한 크기를 초과할 수 없어요!");
            $event->cancel();
        }
    }

    public function onDeath(PlayerDeathEvent $event) : void{

        $player = $event->getEntity();

        if ( $player->getPosition()->getWorld()->getFolderName() !== "wild" ){
            return;
        }

        $economy = EconomyAPI::getInstance();
        $economy->reduceMoney($player, $price = intval($economy->myMoney($player) * 0.05));
        ServerUtils::error($player, "야생에서 사망하여 소지금의 일부, ".$price."원을 잃었어요.");

        if ( $event instanceof EntityDamageByEntityEvent){
            if ( ($damager = $event->getDamager()) instanceof Player ){
                $economy->addMoney($damager, $price);
                ServerUtils::msg($damager, $player->getName()."님의 소지금의 일부, ".$price."원을 약탈했어요.");
            }
        }

    }

}