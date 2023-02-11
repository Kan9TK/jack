<?php

declare(strict_types=1);

namespace kang\piggyceplus\form;

use DaPigGuy\PiggyCustomEnchants\CustomEnchantManager;
use DaPigGuy\PiggyCustomEnchants\enchants\CustomEnchant;
use DaPigGuy\PiggyCustomEnchants\utils\Utils;
use jojoe77777\FormAPI\CustomForm;
use kang\piggyceplus\util\CEBookUtil;
use kang\ServerUtils\ServerUtils;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\world\sound\AnvilFallSound;
use pocketmine\world\sound\AnvilUseSound;

class ExtractForm extends CustomForm {

    protected string $itemName;

    public function __construct(protected Player $player, protected Item $item, protected int $itemIndex)
    {

        $this->itemName = $item->hasCustomName() ? $item->getCustomName() : $item->getName();

        parent::__construct(function(Player $player, $data){
            if(!isset($data))return;

            $itemInHand = $player->getInventory()->getItemInHand();

            if ( $itemInHand->getNamedTag()->getTag(CEBookUtil::COUPON_TAG) == null ) return; // 버그방지
            if ( $itemInHand->getNamedTag()->getTag(CEBookUtil::COUPON_TAG)->getValue() !== CEBookUtil::COUPON_EXTRACT ) return; // 버그방지
            if ( ! $player->getInventory()->getItem($this->itemIndex)->equals($this->item) ) return; // 버그방지

            $randomEnchantment = $this->getCustomEnchantments()[array_rand($this->getCustomEnchantments())];
            $randomEnchantmentType = $randomEnchantment->getType();
            if ( $randomEnchantmentType instanceof CustomEnchant ) {

                $id = $randomEnchantmentType->getId();
                $level = $randomEnchantment->getLevel();
                $this->item->removeEnchantment($randomEnchantmentType); // 해당 아이템 인첸트 제거

                $book = CEBookUtil::createBook($id, $level, 50); // 인첸트북 생성
                $player->getInventory()->addItem($book); // 인첸트북 지급
                $player->getInventory()->setItem($this->itemIndex, $this->item); // 해당 아이템 인첸트 제거
                $player->getInventory()->setItemInHand(VanillaItems::AIR()); // 손에 든 추출 주문서 제거

                $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound()); // 모루

                $color = Utils::getColorFromRarity($randomEnchantmentType->getRarity());
                $name = $randomEnchantmentType->getDisplayName();
                $level = Utils::getRomanNumeral($level);

                ServerUtils::msg($player, "성공적으로 해당 아이템을 추출하여 ".$color.$name." ".$level." §r§7인첸트북을 획득했습니다.");
            }

        });
        $this->setTitle("특수 인첸트 추출");

        $enchantmentNames = [];
        foreach ( $this->getCustomEnchantments() as $enchantment ){
            $enchantmentType = $enchantment->getType();
            if ( $enchantmentType instanceof CustomEnchant ) {

                $color = Utils::getColorFromRarity($enchantmentType->getRarity());
                $name = $enchantmentType->getDisplayName();
                $level = Utils::getRomanNumeral($enchantment->getLevel());

                $enchantmentNames[] = $color.$name." ".$level;
            }
        }

        $this->addLabel([
            " ",
            "§b§l• §r추출할 아이템 §7> §r{$this->itemName}",
            "",
            "§b§l>> §r특수 인첸트 목록 §b§l<<",
            "",
            implode("\n", $enchantmentNames)
        ]);
    }

    /**
     * @return EnchantmentInstance[]
     */
    public function getCustomEnchantments() : array{
        $enchantments = [];
        foreach ( $this->item->getEnchantments() as $enchantment ){
            $enchantmentType = $enchantment->getType();
            if ( $enchantmentType instanceof CustomEnchant ){
                $enchantments[] = $enchantment;
            }
        }
        return $enchantments;
    }

}