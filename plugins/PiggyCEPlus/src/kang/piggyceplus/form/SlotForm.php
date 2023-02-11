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

class SlotForm extends CustomForm {

    protected string $itemName;

    public function __construct(protected Player $player, protected Item $item, protected int $itemIndex)
    {

        $this->itemName = $item->hasCustomName() ? $item->getCustomName() : $item->getName();

        parent::__construct(function(Player $player, $data){
            if(!isset($data))return;

            $itemInHand = $player->getInventory()->getItemInHand();

            if ( $itemInHand->getNamedTag()->getTag(CEBookUtil::COUPON_TAG) == null ) return; // 버그방지
            if ( $itemInHand->getNamedTag()->getTag(CEBookUtil::COUPON_TAG)->getValue() !== CEBookUtil::COUPON_SLOT ) return; // 버그방지
            if ( ! $player->getInventory()->getItem($this->itemIndex)->equals($this->item) ) return; // 버그방지

            $this->item->getNamedTag()->setInt(CEBookUtil::SLOT_TAG, 3);
            $player->getInventory()->setItem($this->itemIndex, $this->item);
            $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound()); // 모루
            ServerUtils::msg($player, "성공적으로 슬롯을 확장했어요.");


        });
        $this->setTitle("특수 인첸트 슬롯 확장");

        $this->addLabel([
            " ",
            "§b§l• §r확장할 아이템 §7> §r{$this->itemName}",
            "",
            "§b§l! §r최대 슬롯이 2개 -> 3개로 확장돼요.",
            "",
        ]);
    }

}