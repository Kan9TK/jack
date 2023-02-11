<?php

declare(strict_types=1);

namespace kang\enchanttableui;

use CLADevs\VanillaX\items\LegacyItemIds;
use kang\enchanttableui\listener\EventListener;
use kang\fishing\item\FishingRod;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\EnchantingTable;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\item\Armor;
use pocketmine\item\Bow;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\Sword;
use pocketmine\item\Tool;
use pocketmine\item\ToolTier;
use pocketmine\plugin\PluginBase;

class EnchantTableUI extends PluginBase{

    protected static EnchantTableUI $instance;

    public static function getInstance() : EnchantTableUI{
        return self::$instance;
    }

    public function onLoad() : void{
        self::$instance = $this;
    }

    protected function onEnable() : void{
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
    }

    public function getBookShelfCount(EnchantingTable $block){
        $pos = $block->getPosition();
        $minX = $pos->x - 2;
        $maxX = $pos->x + 2;
        $minY = $pos->y;
        $maxY = $pos->y + 1;
        $minZ = $pos->z - 2;
        $maxZ = $pos->z + 2;
        $c = 0;
        for($x = $minX; $x <= $maxX; ++$x){
            for ($z = $minZ; $z <= $maxZ; ++$z){
                for ($y = $minY; $y <= $maxY; ++$y){
                    if ( $pos->getWorld()->getBlockAt($x,$y,$z)->getId() === BlockLegacyIds::BOOKSHELF ){
                        $c++;
                    }
                }
            }
        }
        return $c;
    }

    public function getRandomLevel(int $type, int $blockshelfCount) : float{
        if ( $blockshelfCount > 15 ) $blockshelfCount = 15;
        $base = (mt_rand(1,8) + floor( $blockshelfCount / 2) + mt_rand(0, $blockshelfCount));
        if ( $type == 1 ){
            return floor(max($base/3,1));
        }
        elseif ( $type == 2 ){
            return floor(($base*2)/3+1);
        }
        elseif ( $type == 3 ){
            return floor(max($base,$blockshelfCount*2));
        }
        else{
            return floor(max($base/3,1));
        }
    }

    public function isPossibleType(Item $item) : bool{
        if (
            $item instanceof Tool
            or
            $item instanceof Armor
            or
            $item instanceof FishingRod
            or
            $item instanceof Bow
            or
            $item instanceof Sword
        ){
            return true;
        }else{
            return false;
        }
    }

    public function getTypeEnchantments(Item $item) : array{
        $enchantments = [];
        $enchantments[] = EnchantmentIds::UNBREAKING;
        if ( $item instanceof Tool && $item->getBlockToolType() !== BlockToolType::SWORD ){ // SWORD == TOOL
            $enchantments[] = EnchantmentIds::EFFICIENCY;
            $enchantments[] = EnchantmentIds::FORTUNE;
            $enchantments[] = EnchantmentIds::SILK_TOUCH;
        }
        elseif ( $item instanceof Armor ) {
            $enchantments[] = EnchantmentIds::PROTECTION;
            $enchantments[] = EnchantmentIds::FEATHER_FALLING;
            $enchantments[] = EnchantmentIds::FIRE_ASPECT;
            $enchantments[] = EnchantmentIds::PROJECTILE_PROTECTION;
            $enchantments[] = EnchantmentIds::AQUA_AFFINITY;
            $enchantments[] = EnchantmentIds::BLAST_PROTECTION;
            $enchantments[] = EnchantmentIds::RESPIRATION;
            $enchantments[] = EnchantmentIds::DEPTH_STRIDER;
            $enchantments[] = EnchantmentIds::THORNS;
        }
        elseif ( $item instanceof Sword ){
            $enchantments[] = EnchantmentIds::SHARPNESS;
            $enchantments[] = EnchantmentIds::BANE_OF_ARTHROPODS;
            $enchantments[] = EnchantmentIds::SMITE;
            $enchantments[] = EnchantmentIds::KNOCKBACK;
            $enchantments[] = EnchantmentIds::FIRE_ASPECT;
            $enchantments[] = EnchantmentIds::LOOTING;
        }
        elseif ( $item instanceof Bow ) {
            $enchantments[] = EnchantmentIds::POWER;
            $enchantments[] = EnchantmentIds::FLAME;
            $enchantments[] = EnchantmentIds::PUNCH;
            $enchantments[] = EnchantmentIds::INFINITY;
        }
        elseif ( $item instanceof FishingRod ){
            $enchantments[] = EnchantmentIds::LUCK_OF_THE_SEA;
            $enchantments[] = EnchantmentIds::LURE;
        }

        return $enchantments;
    }

    public function getWeight(int $enchantmentId) : int{
        switch ( $enchantmentId ){
            case EnchantmentIds::PROTECTION:
                return 10;
            case EnchantmentIds::FEATHER_FALLING:
                return 5;
            case EnchantmentIds::FIRE_PROTECTION:
                return 5;
            case EnchantmentIds::PROJECTILE_PROTECTION:
                return 5;
            case EnchantmentIds::AQUA_AFFINITY:
                return 2;
            case EnchantmentIds::BLAST_PROTECTION:
                return 2;
            case EnchantmentIds::RESPIRATION:
                return 2;
            case EnchantmentIds::DEPTH_STRIDER:
                return 2;
            case EnchantmentIds::THORNS:
                return 1;

            case EnchantmentIds::SHARPNESS:
                return 10;
            case EnchantmentIds::BANE_OF_ARTHROPODS:
                return 5;
            case EnchantmentIds::KNOCKBACK:
                return 5;
            case EnchantmentIds::SMITE:
                return 5;
            case EnchantmentIds::FIRE_ASPECT:
                return 2;
            case EnchantmentIds::LOOTING:
                return 2;

            case EnchantmentIds::EFFICIENCY:
                return 10;
            case EnchantmentIds::FORTUNE:
                return 2;
            case EnchantmentIds::SILK_TOUCH:
                return 1;

            case EnchantmentIds::POWER:
                return 10;
            case EnchantmentIds::FLAME:
                return 2;
            case EnchantmentIds::PUNCH:
                return 2;
            case EnchantmentIds::INFINITY:
                return 1;

            case EnchantmentIds::LUCK_OF_THE_SEA:
                return 2;
            case EnchantmentIds::LURE:
                return 2;

            case EnchantmentIds::UNBREAKING:
                return 5;

        }
        return 5;
    }

    public function getEnchantAbility(Item $item) : int{
        if ( $item instanceof Sword ){
            switch ( $item->getId() ){
                case ItemIds::WOODEN_SWORD:
                    return 15;
                case ItemIds::STONE_SWORD;
                    return 5;
                case ItemIds::IRON_SWORD:
                    return 14;
                case ItemIds::GOLD_SWORD:
                    return 22;
                case ItemIds::DIAMOND_SWORD:
                    return 10;
                case LegacyItemIds::NETHERITE_SWORD:
                    return 15;
            }
        }
        elseif ( $item instanceof Armor ){
            switch ( $item->getId() ){
                case ItemIds::LEATHER_HELMET:
                case ItemIds::LEATHER_CHESTPLATE:
                case ItemIds::LEATHER_LEGGINGS:
                case ItemIds::LEATHER_BOOTS:
                    return 15;
                case ItemIds::CHAIN_HELMET:
                case ItemIds::CHAIN_CHESTPLATE:
                case ItemIds::CHAIN_LEGGINGS:
                case ItemIds::CHAIN_BOOTS:
                    return 12;
                case ItemIds::IRON_HELMET:
                case ItemIds::IRON_CHESTPLATE:
                case ItemIds::IRON_LEGGINGS:
                case ItemIds::IRON_BOOTS:
                    return 9;
                case ItemIds::GOLD_HELMET:
                case ItemIds::GOLD_CHESTPLATE:
                case ItemIds::GOLD_LEGGINGS:
                case ItemIds::GOLD_BOOTS:
                    return 25;
                case ItemIds::DIAMOND_HELMET:
                case ItemIds::DIAMOND_CHESTPLATE:
                case ItemIds::DIAMOND_LEGGINGS:
                case ItemIds::DIAMOND_BOOTS:
                    return 10;
                case LegacyItemIds::NETHERITE_HELMET:
                case LegacyItemIds::NETHERITE_CHESTPLATE:
                case LegacyItemIds::NETHERITE_LEGGINGS:
                case LegacyItemIds::NETHERITE_BOOTS:
                    return 15;
            }
        }
        return 1;
    }

    public function getEnchantment(Item $item, int $level) : array{
        $abilityLevel = $this->getAbilityLevel($item, $level);

        $enchantmentLevels = $this->getEnchantmentLevels($abilityLevel);
        $possibleEnchantments = $this->getTypeEnchantments($item);

        $enchantments = [];
        $totalWeight = 0;
        foreach ( $enchantmentLevels as $enchantment => $level ){
            if ( in_array($enchantment, $possibleEnchantments) && $level > 0 ){
                $enchantments[$enchantment] = $level;
                $totalWeight += $this->getWeight($enchantment);
            }
        }

        $baseEnchantmentId = EnchantmentIds::UNBREAKING;
        $baseEnchantmentLevel = 0;

        $weight = mt_rand(0, $totalWeight/2);

        while($weight >= 0){

            foreach ($enchantments as $enchantment => $level){
                $weight -= $this->getWeight($enchantment);
                if ( $weight < 0 ){
                    $baseEnchantmentId = $enchantment;
                    $baseEnchantmentLevel = $level;
                    break;
                }
            }
        }

        unset($enchantments[$baseEnchantmentId]); // 첫번째 인첸트 가능 항목에서 제거
        $enchantments = $this->checkIncompatible($baseEnchantmentId, $enchantments); // 첫번재 인첸트 적대 인첸트들 항목에서 제거

        $chosenEnchantments = []; //최종 인첸트 추가
        $chosenEnchantments[$baseEnchantmentId] = $baseEnchantmentLevel; // 최종 인첸트에 첫번째 인첸트 추가.

        while ( true ){

            $bonusRand = (($abilityLevel+1)/50) * 100;

            if ( $bonusRand >= mt_rand(1,100) ){

                $weight = mt_rand(0, $totalWeight/2);
                $bonusEnchantments = []; // 보너스 인첸트 목록 생성
                
                $enchantmentLevels = $this->getEnchantmentLevels((int)$abilityLevel); // 어빌리티레벨로 레벨재검색
                foreach ( $enchantmentLevels as $enchantment => $level ) {
                    if (
                        isset ( $enchantments[$enchantment] ) // 가능 항목에 존재하는지 체크
                        &&
                        $level > 0 // 레벨이 0 이상만
                    ) {
                        $bonusEnchantments[$enchantment] = $level; // 보너스 인첸트 가능 항목에 추가
                    }
                }

                while($weight >= 0){
                    foreach ($bonusEnchantments as $enchantment => $level){ // 보너스 인첸트 항목
                        $weight -= $this->getWeight($enchantment);
                        if ( $weight < 0 ){
                            $chosenEnchantments[$enchantment] = $level; // 최종 인첸트에 추가
                            unset($bonusEnchantments[$enchantment]); // 보너스 인첸트 가능 항목에서 제거
                            break;
                        }
                    }
                }

                $abilityLevel = round($abilityLevel / 2); // 어빌리티 레벨 반띵 보너스 인첸트 반복

            }else{
                break;
            }
        }

        return $chosenEnchantments;
    }

    public function randomInt($n) : int{
        return mt_rand(0,intval($n-1));
    }

    public function getAbilityLevel(Item $item, int $level){
        $enchantAbility = $this->getEnchantAbility($item);
        $enchantAbility = $enchantAbility / 2;
        $rand_enchantAbility = 1 + $this->randomInt(($enchantAbility/4)+1) + $this->randomInt(($enchantAbility/4)+1);
        $k = $level + $rand_enchantAbility;

        $rand_bonus_percent = 1 + (((mt_rand() / mt_getrandmax()) + (mt_rand() / mt_getrandmax()) - 1) * 0.15);
        $final_level = round($k * $rand_bonus_percent);
        if ( $final_level < 1) $final_level = 1;
        return (int)$final_level;
    }

    public function getEnchantmentLevels(int $abilityLevel) : array{
        $enchantments = [];
        $levels = [
            EnchantmentIds::PROTECTION => [0, 1, 12, 23, 34],
            EnchantmentIds::FIRE_PROTECTION => [0, 10, 18, 26, 34],
            EnchantmentIds::FEATHER_FALLING => [0, 5, 11, 17, 23],
            EnchantmentIds::BLAST_PROTECTION => [0, 5, 13, 21, 29],
            EnchantmentIds::PROJECTILE_PROTECTION => [0, 3, 9, 15, 21],
            EnchantmentIds::THORNS => [0, 10, 30, 50],
            EnchantmentIds::RESPIRATION => [0, 10, 20, 30],
            EnchantmentIds::DEPTH_STRIDER => [0, 10, 20, 30],
            EnchantmentIds::AQUA_AFFINITY => [0, 1],
            EnchantmentIds::SHARPNESS => [0, 1, 12, 23, 34, 45],
            EnchantmentIds::SMITE => [0, 5, 13, 21, 29, 37],
            EnchantmentIds::BANE_OF_ARTHROPODS => [0, 5, 13, 21, 29, 37],
            EnchantmentIds::KNOCKBACK => [0, 5, 25],
            EnchantmentIds::FIRE_ASPECT => [0, 10, 30],
            EnchantmentIds::LOOTING => [0, 15, 24, 33],
            EnchantmentIds::EFFICIENCY => [0, 1, 11, 21, 31, 41],
            EnchantmentIds::SILK_TOUCH => [0, 15],
            EnchantmentIds::UNBREAKING => [0, 5, 13, 21],
            EnchantmentIds::FORTUNE => [0, 15, 24, 33],
            EnchantmentIds::POWER => [0, 1, 11, 21, 31, 41],
            EnchantmentIds::PUNCH => [0, 12, 32],
            EnchantmentIds::FLAME => [0, 20],
            EnchantmentIds::INFINITY => [0, 20],
            EnchantmentIds::LUCK_OF_THE_SEA => [0, 15, 24, 33],
            EnchantmentIds::LURE => [0, 15, 24, 33],
            EnchantmentIds::FROST_WALKER => [0, 10, 20],
            EnchantmentIds::MENDING => [0, 25],
            EnchantmentIds::VANISHING => [0, 25],
        ];
        foreach ($levels as $enchantment => $level) {
            foreach ($level as $index => $min) {
                if ($abilityLevel >= $min) {
                    $enchantments[$enchantment] = $index;
                }
            }
        }
        return $enchantments;
    }

    public function checkIncompatible(int $enchantmentId, array $enchantments) : array{

        $incompatible = [];

        $incompatible[EnchantmentIds::BANE_OF_ARTHROPODS] = [
            EnchantmentIds::SMITE,
            EnchantmentIds::SHARPNESS
        ];

        $incompatible[EnchantmentIds::BLAST_PROTECTION] = [
            EnchantmentIds::FIRE_PROTECTION,
            EnchantmentIds::PROTECTION,
            EnchantmentIds::PROJECTILE_PROTECTION
        ];

        $incompatible[EnchantmentIds::CHANNELING] = [
            EnchantmentIds::RIPTIDE
        ];

        $incompatible[EnchantmentIds::DEPTH_STRIDER] = [
            EnchantmentIds::FROST_WALKER
        ];

        $incompatible[EnchantmentIds::FIRE_PROTECTION] = [
            EnchantmentIds::BLAST_PROTECTION,
            EnchantmentIds::PROJECTILE_PROTECTION,
            EnchantmentIds::PROTECTION
        ];

        $incompatible[EnchantmentIds::FORTUNE] = [
            EnchantmentIds::SILK_TOUCH
        ];

        $incompatible[EnchantmentIds::INFINITY] = [
            EnchantmentIds::MENDING
        ];

        $incompatible[EnchantmentIds::LOYALTY] = [
            EnchantmentIds::RIPTIDE
        ];

        $incompatible[EnchantmentIds::MENDING] = [
            EnchantmentIds::INFINITY
        ];

        $incompatible[EnchantmentIds::MULTISHOT] = [
            EnchantmentIds::PIERCING
        ];

        $incompatible[EnchantmentIds::PROJECTILE_PROTECTION] = [
            EnchantmentIds::PROTECTION,
            EnchantmentIds::BLAST_PROTECTION,
            EnchantmentIds::FIRE_PROTECTION
        ];

        $incompatible[EnchantmentIds::PROTECTION] = [
            EnchantmentIds::PROJECTILE_PROTECTION,
            EnchantmentIds::BLAST_PROTECTION,
            EnchantmentIds::FIRE_PROTECTION
        ];

        $incompatible[EnchantmentIds::RIPTIDE] = [
            EnchantmentIds::CHANNELING,
            EnchantmentIds::LOYALTY
        ];

        $incompatible[EnchantmentIds::SHARPNESS] = [
            EnchantmentIds::BANE_OF_ARTHROPODS,
            EnchantmentIds::SMITE
        ];

        $incompatible[EnchantmentIds::SILK_TOUCH] = [
            EnchantmentIds::FORTUNE
        ];

        $incompatible[EnchantmentIds::SMITE] = [
            EnchantmentIds::BANE_OF_ARTHROPODS,
            EnchantmentIds::SHARPNESS
        ];

        if ( isset ( $incompatible[$enchantmentId] ) ){
            foreach ( $incompatible[$enchantmentId] as $incompatibleEnchantment ){
                if ( isset ( $enchantments[$incompatibleEnchantment] ) ){
                    unset($enchantments[$incompatibleEnchantment]);
                }
            }
        }

        return $enchantments;
    }

}