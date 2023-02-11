<?php

declare(strict_types=1);

namespace kang\enchanttableui\inventory;

use kang\enchanttableui\EnchantTableUI;
use kang\enchanttableui\form\EnchantCheckForm;
use kang\piggyceplus\form\ErrorForm;
use kang\piggyceplus\util\CEBookUtil;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class SelectItemInventory{

    protected InvMenu $menu;
    protected array $itemIndex = [];

    public function __construct(protected Player $player, protected int $type, protected int $level){
        $this->menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $this->menu->setName("인첸트할 아이템을 선택해주세요.");
        $this->menu->setListener(\Closure::fromCallable([$this, "onTransaction"]));
        $this->menu->setInventoryCloseListener(\Closure::fromCallable([$this, "onClose"]));

        $bedrockSlots = [0,1,2,3,4,5,6,7,8,45,46,47,48,49,50,51,52,53];
        foreach ( $bedrockSlots as $bedrockSlot ){
            $item = ItemFactory::getInstance()->get(ItemIds::BED_BLOCK);
            $item->getNamedTag()->setString("bedrock", "");
            $this->menu->getInventory()->setItem($bedrockSlot, $item);
        }

        $i = 9;
        foreach ( $player->getInventory()->getContents(false) as $index => $item ){
            if ( ! $item->hasEnchantments() && EnchantTableUI::getInstance()->isPossibleType($item) ){
                $this->itemIndex[$i] = $index;
                $this->menu->getInventory()->setItem($i++, $item);
            }
        }
    }

    public function onTransaction(InvMenuTransaction $action) : InvMenuTransactionResult{
        $item = $action->getOut();
        $index = $action->getAction()->getSlot();

        if ( $item->getNamedTag()->getTag("bedrock") !== null ){
            return $action->discard();
        }

        $this->player->removeCurrentWindow();
        return $action->discard()->then(function() use($item,$index)  : void{
            $this->player->sendForm(new EnchantCheckForm($this->player, $this->type, $this->level, $item, $this->itemIndex[$index]));
        });
    }

    public function onClose() : void{

    }

    public function send() : void{
        $this->menu->send($this->player);
    }

}