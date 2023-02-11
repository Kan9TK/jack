<?php

declare(strict_types=1);

namespace kang\piggyceplus\inventory;

use DaPigGuy\PiggyCustomEnchants\CustomEnchantManager;
use DaPigGuy\PiggyCustomEnchants\enchants\CustomEnchant;
use DaPigGuy\PiggyCustomEnchants\utils\Utils;
use kang\enchanttableui\EnchantTableUI;
use kang\enchanttableui\form\EnchantCheckForm;
use kang\piggyceplus\form\EnchantForm;
use kang\piggyceplus\form\ErrorForm;
use kang\piggyceplus\form\ExtractForm;
use kang\piggyceplus\form\SlotForm;
use kang\piggyceplus\util\CEBookUtil;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class SlotSelectItemInventory{

    protected InvMenu $menu;
    protected array $itemIndex = [];

    public function __construct(protected Player $player){
        $this->menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $this->menu->setName("확장할 아이템을 선택해주세요.");
        $this->menu->setListener(\Closure::fromCallable([$this, "onTransaction"]));
        $this->menu->setInventoryCloseListener(\Closure::fromCallable([$this, "onClose"]));

        $bedrockSlots = [0, 1, 2, 3, 4, 5, 6, 7, 8, 45, 46, 47, 48, 49, 50, 51, 52, 53];
        foreach ($bedrockSlots as $bedrockSlot) {
            $item = ItemFactory::getInstance()->get(ItemIds::BED_BLOCK);
            $item->getNamedTag()->setString("bedrock", "");
            $this->menu->getInventory()->setItem($bedrockSlot, $item);
        }

        $i = 9;
        foreach ($player->getInventory()->getContents(false) as $index => $item) {

            if ( CEBookUtil::getCECount($item) < 1 ){
                continue;
            }

            if ( CEBookUtil::getSlot($item) > 2 ){
                continue;
            }

            $this->itemIndex[$i] = $index;
            $this->menu->getInventory()->setItem($i++, $item);
        }
    }

    public function onTransaction(InvMenuTransaction $action) : InvMenuTransactionResult{
        $item = $action->getOut();

        if ( ! isset ( $this->itemIndex[$action->getAction()->getSlot()] ) ){
            return $action->discard();
        }

        if ( $item->getNamedTag()->getTag("bedrock") !== null ){
            return $action->discard();
        }

        $itemIndex = $this->itemIndex[$action->getAction()->getSlot()];

        $this->player->removeCurrentWindow();
        return $action->discard()->then(function() use($item,$itemIndex)  : void{
            $this->player->sendForm(new SlotForm($this->player, $item, $itemIndex));
        });
    }

    public function onClose() : void{

    }

    public function send() : void{
        $this->menu->send($this->player);
    }

}