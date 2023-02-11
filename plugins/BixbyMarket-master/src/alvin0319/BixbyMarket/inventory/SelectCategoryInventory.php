<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\inventory;

use alvin0319\BixbyMarket\shop\Shop;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class SelectCategoryInventory{

    private InvMenu $menu;

    public function __construct(private Player $player, private ?Shop $shop = null){
        $this->menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $this->menu->setName("카테고리 선택");
        $this->menu->setListener(\Closure::fromCallable([$this, "onTransaction"]));
        $this->menu->setInventoryCloseListener(\Closure::fromCallable([$this, "onClose"]));

        $this->settingCategory();
    }

    public function onTransaction(InvMenuTransaction $action) : InvMenuTransactionResult
    {

        $item = $action->getOut();

        if ($item->getNamedTag()->getTag("category") === null) {
            return $action->discard();
        }

        $categoryName = $item->getNamedTag()->getTag("category")->getValue();
        $category = $this->shop->getCategory($categoryName);
        if ($category !== null) {

            $this->player->removeCurrentWindow();
            $inv = new MarketInventory($this->player, $this->shop, $category);
            $inv->send();

        }
        return $action->discard();
    }

    public function onClose() : void{
    }

    public function settingCategory(){
        foreach($this->shop->getCategories() as $categoryIndex => $category){
            $item = $category->getItem();
            $item->setCustomName(TextFormat::RESET.TextFormat::BOLD.$category->getName());
            $item->getNamedTag()->setString("category", $category->getName());
            $this->menu->getInventory()->setItem($categoryIndex, $item);
        }
    }

    public function send() : void{
        $this->menu->send($this->player);
    }
    
}