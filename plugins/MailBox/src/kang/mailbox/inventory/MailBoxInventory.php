<?php

declare(strict_types=1);

namespace kang\mailbox\inventory;

use kang\mailbox\MailBox;
use kang\ServerUtils\ServerUtils;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\block\BlockLegacyIds as BlockIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class MailBoxInventory{

    protected InvMenu $menu;
    protected int $page = 0;

    public function __construct(protected Player $player){
        $this->menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $this->menu->setName("우편함");
        $this->menu->setListener(\Closure::fromCallable([$this, "onTransaction"]));
        $this->menu->setInventoryCloseListener(\Closure::fromCallable([$this, "onClose"]));
        $this->setting();
    }

    public function onTransaction(InvMenuTransaction $action) : InvMenuTransactionResult{
        $item = $action->getOut();
        if($item->getNamedTag()->getTag("previous") !== null){
            if ( $this->page > 0 ){
                $this->page--;
                $this->setting();
            }
        }
        if($item->getNamedTag()->getTag("next") !== null){
            $this->page++;
            $this->setting();
        }
        if($item->getNamedTag()->getTag("getall") !== null){
            MailBox::getAllItem($this->player);
            $this->player->removeCurrentWindow();
        }
        if($item->getNamedTag()->getTag("mail") !== null){
            $mailItem = MailBox::getItem($this->player, $action->getAction()->getSlot()+($this->page*45));
            if ( $item->getName() !== $mailItem->getName() ){
                $this->setting();
                return $action->discard();
            }
            if ( $this->player->getInventory()->canAddItem($mailItem) ){
                $this->player->getInventory()->addItem($mailItem);
                MailBox::removeItem($this->player, $action->getAction()->getSlot()+($this->page*45));
                $this->setting();;
            }
        }
        return $action->discard();
    }

    public function onClose() : void{

    }

    public function setting(){

        MailBox::checkExpire($this->player);

        $this->menu->getInventory()->clearAll();
        $this->settingItems();
        $this->settingButtons();
    }

    public function settingItems(){
        foreach ( MailBox::getItems($this->player) as $index => $itemData ){
            if ( $index >= $this->page*45 && $index <= ($this->page+1)*45 ){
                $item = Item::jsonDeserialize($itemData["nbt"]);
                $lore = $item->getLore();
                $lore[] = "\n§r사유: ".$itemData["reason"];
                $lore[] = "§r만료: ".date("Y-m-d H:i:s",$itemData["expire"]);
                $lore[] = "§r끌어오거나 클릭하여 가져오기";
                $item->setLore($lore);
                $item->getNamedTag()->setString("mail", "");
                $this->menu->getInventory()->addItem($item);
            }
        }
    }

    public function settingButtons(){
        $inv = $this->menu->getInventory();

        $item = ItemFactory::getInstance()->get(ItemIds::PAPER, 0, 1)
            ->setCustomName("§r이전 페이지")
            ->setLore(["§r현재 페이지: ".($this->page+1)]);
        $item->getNamedTag()->setString("previous", "");
        $inv->setItem(48, $item);

        $item = VanillaBlocks::CHEST()->asItem()
            ->setCustomName("§r보관 현황");
        $lore = $item->getLore();
        $lore[] = "\n§r총 ".MailBox::getAllItemCount($this->player)."개의 아이템을 보관 중이에요.";
        $item->setLore($lore);
        $item->getNamedTag()->setString("info", "");
        $inv->setItem(49, $item);

        $item = ItemFactory::getInstance()->get(ItemIds::PAPER, 0, 1)
            ->setCustomName("§r다음 페이지")
            ->setLore(["§r현재 페이지: ".($this->page+1)]);
        $item->getNamedTag()->setString("next", "");
        $inv->setItem(50, $item);

        $item = ItemFactory::getInstance()->get(BlockIds::BED_BLOCK, 0, 1)
            ->setCustomName("§r전체 가져오기");
        $item->getNamedTag()->setString("getall", "");
        $inv->setItem(53, $item);
    }

    public function send(){
        $this->menu->send($this->player);
    }

}