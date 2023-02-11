<?php

declare(strict_types=1);

namespace kang\enchanttableui\form;

use jojoe77777\FormAPI\ModalForm;
use kang\enchanttableui\EnchantTableUI;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\player\Player;
use pocketmine\world\sound\AnvilUseSound;

class EnchantCheckForm extends ModalForm{

    public function __construct(protected Player $player, protected int $type, protected int $level, protected Item $selectItem, protected int $selectItemIndex){
        parent::__construct(function(Player $player, $data){
            if(!isset($data))return;
            switch($data){
                case true:
                    $this->enchantItem();
                    break;
                case false:
                    break;
            }
        });

        $name = $this->selectItem->hasCustomName() ? $this->selectItem->getCustomName() : $this->selectItem->getName();

        $this->setTitle("인첸트");
        $this->setContent([
            " ",
            "인첸트할 아이템: ".$name,
            "소모 레벨: ".$type,
            "소모 청금석: ".$type."개",
            " ",
            "정말 인첸트할까요?",
            " "
        ]);
        $this->setButton1("인첸트할게요.");
        $this->setButton2("취소할게요.");
    }

    public function enchantItem() : void{

        if ( ! $this->player->getInventory()->getItem($this->selectItemIndex)->equals($this->selectItem) ) return; // 버그방지

        $plugin = EnchantTableUI::getInstance();

        $enchantItem = clone $this->selectItem;

        $enchantments = $plugin->getEnchantment($enchantItem, $this->level);

        foreach ( $enchantments as $enchantment => $level ){
            $enchantItem->addEnchantment($enchantmentInstance = new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId($enchantment), $level));
        }

        $this->player->getInventory()->setItem($this->selectItemIndex, VanillaItems::AIR());

        $this->player->getInventory()->addItem($enchantItem);

        $this->player->getWorld()->addSound($this->player->getPosition(), new AnvilUseSound());

        $xpLevel = $this->player->getXpManager()->getXpLevel();
        $this->player->getXpManager()->setXpLevel($xpLevel - $this->type);
        $this->player->getInventory()->removeItem(ItemFactory::getInstance()->get(ItemIds::LAPIS_ORE,$this->type));
    }

}