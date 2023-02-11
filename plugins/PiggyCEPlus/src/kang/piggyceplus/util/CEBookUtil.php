<?php

declare(strict_types=1);

namespace kang\piggyceplus\util;

use DaPigGuy\PiggyCustomEnchants\CustomEnchantManager;
use DaPigGuy\PiggyCustomEnchants\enchants\CustomEnchant;
use DaPigGuy\PiggyCustomEnchants\utils\Utils;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\utils\TextFormat;

class CEBookUtil{

    public const COUPON_NAME = [
        self::COUPON_PROTECT => "보호",
        self::COUPON_EXTRACT => "추출",
        self::COUPON_SLOT => "슬롯"
    ];

    public const COUPON_TAG = "CECoupon";
    public const COUPON_PROTECT = "protect";
    public const COUPON_EXTRACT = "extract";
    public const COUPON_SLOT = "slot";

    public const BOOK_TAG = "CEBook";
    public const SLOT_TAG = "CESlot";

    public static function createBook(int $id, int $level, int $successPer = 100) : ?Item{

        $em = CustomEnchantManager::getEnchantment($id);
        if ($em === null) {
            return null;
        }
        if ( $level > $em->getMaxLevel() ){
            $level = $em->getMaxLevel();
        }

        if ( $successPer > 100 ){
            $successPer = 100;
        }
        $failurePer = 100 - $successPer;

        $item = ItemFactory::getInstance()->get(ItemIds::ENCHANTED_BOOK);
        if ( $item instanceof Item ){

            $name = $em->getDisplayName();
            $color = Utils::getColorFromRarity($em->getRarity());
            $romanLevel = Utils::getRomanNumeral($level);
            $type = Utils::TYPE_NAMES[$em->getItemType()];

            $item->setCustomName("§r§l".$color.$name." ".$romanLevel);
            $item->setLore([
                "§r§b§l• §r분류 §7> §r".$type,
                "§r§b§l• §r설명 §7> §r".$em->getDescription(),
                "",
                "§r§b§l• §r성공 §7> §r".$successPer."%",
                "§r§b§l• §r실패 §7> §r".$failurePer."%",
            ]);
            $item->getNamedTag()->setInt(self::BOOK_TAG, $id);
            $item->getNamedTag()->setInt("level", $level);
            $item->getNamedTag()->setInt("success", $successPer);
            $item->getNamedTag()->setInt("failure", $failurePer);
            return $item;
        }

        return null;

    }

    public static function createProtectCoupon() : ?Item{
        $item = ItemFactory::getInstance()->get(ItemIds::ENCHANTED_BOOK);
        if ( $item instanceof Item ){
            $item->setCustomName("§r§f§l특수인첸트 보호 주문서");
            $item->setLore([
                " ",
                "§r§b§l• §r설명 §7> §r특수 인첸트의 파괴를 1회 방지해요.",
            ]);
            $item->getNamedTag()->setString(self::COUPON_TAG, self::COUPON_PROTECT);
            return $item;
        }
        return null;
    }
    
    public static function createExtractCoupon() : ?Item{
        $item = ItemFactory::getInstance()->get(ItemIds::ENCHANTED_BOOK);
        if ( $item instanceof Item ){
            $item->setCustomName("§r§f§l특수인첸트 추출 주문서");
            $item->setLore([
                " ",
                "§r§b§l• §r설명 §7> §r특수 인첸트 중 1개를 무작위로 추출해요.",
                "",
                "§r§b§l! §r§7웅크려서 사용할 수 있어요.",
                "",
                "§r§c§l! §r§7성공 확률 50%의 인첸트 북으로 추출돼요."
            ]);
            $item->getNamedTag()->setString(self::COUPON_TAG, self::COUPON_EXTRACT);
            return $item;
        }
        return null;
    }

    public static function createSlotCoupon() : ?Item{
        $item = ItemFactory::getInstance()->get(ItemIds::ENCHANTED_BOOK);
        if ( $item instanceof Item ){
            $item->setCustomName("§r§f§l특수인첸트 확장 주문서");
            $item->setLore([
                " ",
                "§r§b§l• §r설명 §7> §r특수 인첸트 슬롯을 3개로 확장해요.",
                "",
                "§r§b§l! §r§7웅크려서 사용할 수 있어요.",
                "",
                "§r§c§l! §r§7기본 슬롯은 2개, 최대 슬롯은 3개에요."
            ]);
            $item->getNamedTag()->setString(self::COUPON_TAG, self::COUPON_SLOT);
            return $item;
        }
        return null;
    }

    public static function getCECount(Item $item) : int{
        $c=0;
        foreach ($item->getEnchantments() as $enchantment) {
            $enchantmentType = $enchantment->getType();
            if ( $enchantmentType instanceof CustomEnchant ){
                $c++;
            }
        }
        return $c;
    }

    public static function getSlot(Item $item) : int{
        if ( $item->getNamedTag()->getTag(self::SLOT_TAG) == null ){
            return 2;
        }else{
            return $item->getNamedTag()->getTag(self::SLOT_TAG)->getValue();
        }
    }

}