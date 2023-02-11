<?php

declare(strict_types=1);

namespace kang\piggyceplus\inventory;

use kang\enchanttableui\EnchantTableUI;
use kang\enchanttableui\form\EnchantCheckForm;
use kang\piggyceplus\form\EnchantForm;
use kang\piggyceplus\form\ErrorForm;
use kang\piggyceplus\util\CEBookUtil;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class SelectBookInventory{

    protected InvMenu $menu;
    protected array $bookIndex = [];

    public function __construct(protected Player $player){
        $this->menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $this->menu->setName("사용할 인첸트북을 선택해주세요.");
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
            if ( $item->getNamedTag()->getTag("CEBook") !== null ){
                $this->bookIndex[$i] = $index;
                $this->menu->getInventory()->setItem($i++, $item);
            }
        }
    }

    public function onTransaction(InvMenuTransaction $action) : InvMenuTransactionResult{
        $book = $action->getOut();

        if ( ! isset ( $this->bookIndex[$action->getAction()->getSlot()] ) ){
            return $action->discard();
        }
        if ( $book->getNamedTag()->getTag("bedrock") !== null ){
            return $action->discard();
        }

        $bookIndex = $this->bookIndex[$action->getAction()->getSlot()];

        $inv = new SelectItemInventory($this->player, $book, $bookIndex);
        $inv->send();
        return $action->discard();
    }

    public function onClose() : void{

    }

    public function send() : void{
        $this->menu->send($this->player);
    }

}