<?php

declare(strict_types=1);

namespace kang\piggyceplus\inventory;

use DaPigGuy\PiggyCustomEnchants\CustomEnchantManager;
use DaPigGuy\PiggyCustomEnchants\enchants\CustomEnchant;
use DaPigGuy\PiggyCustomEnchants\enchants\Rarity;
use DaPigGuy\PiggyCustomEnchants\utils\Utils;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\block\BlockLegacyIds;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class EnchantListInventory{

    protected InvMenu $menu;
    protected bool $send = false;

    public function __construct(protected Player $player){
        $this->menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $this->menu->setName("특수 인첸트 목록");
        $this->menu->setListener(\Closure::fromCallable([$this, "onTransaction"]));
        $this->settingIcons();
    }

    public function settingIcons() : void{
        if ( $this->send == true ) {
            $this->player->removeCurrentWindow();
            $this->menu = InvMenu::create(InvMenu::TYPE_CHEST);
            $this->menu->setName("특수 인첸트 목록");
            $this->menu->setListener(\Closure::fromCallable([$this, "onTransaction"]));
            $this->menu->getInventory()->clearAll();
        }
        $raritySlots = [
            Rarity::NORMAL => 11,
            Rarity::RARE => 12,
            Rarity::EPIC => 13,
            Rarity::UNIQUE => 14,
            Rarity::LEGENDARY => 15
        ];
        $rarityIcons = [
            Rarity::NORMAL => ItemFactory::getInstance()->get(BlockLegacyIds::STAINED_GLASS, 0),
            Rarity::RARE => ItemFactory::getInstance()->get(BlockLegacyIds::STAINED_GLASS, 3),
            Rarity::EPIC => ItemFactory::getInstance()->get(BlockLegacyIds::STAINED_GLASS, 4),
            Rarity::UNIQUE => ItemFactory::getInstance()->get(BlockLegacyIds::STAINED_GLASS, 10),
            Rarity::LEGENDARY => ItemFactory::getInstance()->get(BlockLegacyIds::STAINED_GLASS, 5)
        ];
        foreach ( Utils::RARITY_NAMES as $rarity => $RARITY_NAME ){
            $item = $rarityIcons[$rarity];
            $item->setCustomName(TextFormat::RESET.TextFormat::BOLD.Utils::getColorFromRarity($rarity).$RARITY_NAME." 인첸트");
            $item->setLore([TextFormat::RESET."끌어오거나 클릭하여 목록을 확인해요."]);
            $item->getNamedTag()->setInt("icon", $rarity);
            $this->menu->getInventory()->setItem($raritySlots[$rarity], $item);
        }

        if ( $this->send == true ) {
            $this->menu->send($this->player);
        }

    }

    public function settingBooks(int $rarity){
        $this->send = true;
        $this->player->removeCurrentWindow();
        $this->menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $this->menu->setName(Utils::RARITY_NAMES[$rarity]." 인첸트 목록");
        $this->menu->setListener(\Closure::fromCallable([$this, "onTransaction"]));
        $this->menu->getInventory()->clearAll();

        $enchantmentsByRarity = array_filter(CustomEnchantManager::getEnchantments(), function($var) use($rarity){
            return $var->getRarity() == $rarity;
        });

        foreach ( $enchantmentsByRarity as $enchantment ){
            $item = ItemFactory::getInstance()->get(ItemIds::ENCHANTED_BOOK);
            $item->setCustomName(TextFormat::RESET.TextFormat::BOLD.$enchantment->getDisplayName()." 인첸트");
            $item->setLore([
                "§b§l• §r이름 §7> §r".$enchantment->getDisplayName(),
                "§b§l• §r등급 §7> §r".Utils::getColorFromRarity($rarity).Utils::RARITY_NAMES[$rarity],
                "§b§l• §r분류 §7> §r".Utils::TYPE_NAMES[$enchantment->getItemType()],
                "§b§l• §r최대 §7> §r".Utils::getRomanNumeral($enchantment->getMaxLevel()),
                "§b§l• §r설명 §7> §r".$enchantment->getDescription()
            ]);
            $this->menu->getInventory()->addItem($item);
        }

        $backSlot = 49;
        $item = VanillaItems::PAPER();
        $item->setCustomName(TextFormat::RESET."뒤로가기");
        $item->getNamedTag()->setString("back", "");
        $this->menu->getInventory()->setItem($backSlot, $item);

        $this->menu->send($this->player);

    }

    public function onTransaction(InvMenuTransaction $transaction) : InvMenuTransactionResult{
        $item = $transaction->getOut();
        if ( $item->getNamedTag()->getTag("icon") !== null ){
            $this->settingBooks($item->getNamedTag()->getTag("icon")->getValue());
        }
        if ( $item->getNamedTag()->getTag("back") !== null ){
            $this->settingIcons();
        }
        return $transaction->discard();
    }

    public function send() : void{
        $this->menu->send($this->player);
    }

}