<?php

declare(strict_types=1);

namespace kang\piggyceplus\form;

use DaPigGuy\PiggyCustomEnchants\CustomEnchantManager;
use jojoe77777\FormAPI\CustomForm;
use kang\piggyceplus\util\CEBookUtil;
use kang\ServerUtils\ServerUtils;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\world\sound\AnvilFallSound;
use pocketmine\world\sound\AnvilUseSound;

class EnchantForm extends CustomForm {

    protected string $itemName;
    protected string $bookName;
    protected int $successPer;
    protected int $failurePer;

    public function __construct(protected Player $player, protected Item $item, protected int $itemIndex, protected Item $book, protected int $bookIndex)
    {

        $this->itemName = $item->hasCustomName() ? $item->getCustomName() : $item->getName();
        $this->bookName = $book->hasCustomName() ? $book->getCustomName() : $book->getName();
        $this->successPer = $book->getNamedTag()->getTag("success")->getValue();
        $this->failurePer = $book->getNamedTag()->getTag("failure")->getValue();

        parent::__construct(function(Player $player, $data){
            if(!isset($data))return;

            if ( ! $player->getInventory()->getItem($this->itemIndex)->equals($this->item) ) return; // 버그방지
            if ( ! $player->getInventory()->getItem($this->bookIndex)->equals($this->book) ) return; // 버그방지

            if ( $data[1] === true ){
                if ( $this->getCouponCount() < 1 ){
                    ServerUtils::error($player, "보호 주문서를 보유하고 있지 않아요.");
                    return;
                }
            }

            if (mt_rand(0 * 100000, 100 * 100000) / 100000 <= $this->successPer ){

                //success
                $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());

                $item = clone $this->item;

                $id = $this->book->getNamedTag()->getTag("CEBook")->getValue();
                $level = $this->book->getNamedTag()->getTag("level")->getValue();

                $enchantment = CustomEnchantManager::getEnchantment($id);
                $item->addEnchantment(new EnchantmentInstance($enchantment, $level));

                $player->getInventory()->setItem($this->itemIndex, VanillaItems::AIR()); // 새로운 아이템 교체를 위한 제거
                $player->getInventory()->setItem($this->bookIndex, VanillaItems::AIR()); // 인첸트북 소모
                $player->getInventory()->addItem($item); // 새로운 아이템 지급


            }else{

                //failure
                $player->getWorld()->addSound($player->getPosition(), new AnvilFallSound());
                $player->getInventory()->setItem($this->bookIndex, VanillaItems::AIR()); // 인첸트북 소모

                if ( $data[1] === true ){
                    ServerUtils::msg($player, "인첸트에 실패했지만 보호 주문서를 사용하여 파괴를 방지했어요.");
                    $player->getInventory()->removeItem(CEBookUtil::createProtectCoupon()); // 보호 주문서 소모
                }else{
                    ServerUtils::msg($player, "인첸트에 실패하여 아이템이 파괴되었어요.");
                    $player->getInventory()->setItem($this->itemIndex, VanillaItems::AIR());  // 아이템 파괴
                }

            }

        });
        $this->setTitle("특수 인첸트");

        $this->addLabel([
            " ",
            "§b§l• §r인첸트할 아이템 §7> §r{$this->itemName}",
            "",
            "§b§l• §r사용할 인첸트북 §7> §r{$this->bookName}",
            "§b§l• §r성공 확률 §7> §r{$this->successPer}%",
            "§b§l• §r실패 확률 §7> §r{$this->failurePer}%",
            "",
            "§b§l! §r§7보호 주문서 보유 갯수: {$this->getCouponCount()}개",
            "§c§l! §r§7보호 주문서를 적용하지 않으면 실패 시 파괴돼요.",
            "",
        ]);
        
        $this->addToggle("보호 주문서 사용");
    }

    public function getCouponCount() : int{
        $couponCount = 0;
        $protectCoupon = CEBookUtil::createProtectCoupon();
        foreach ( $this->player->getInventory()->getContents() as $content ){
            if ( $protectCoupon->equals($content) ){
                $couponCount++;
            }
        }
        return $couponCount;
    }

}