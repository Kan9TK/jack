<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\inventory;

use alvin0319\BixbyMarket\BixbyMarket;
use alvin0319\BixbyMarket\category\Category;
use alvin0319\BixbyMarket\form\MarketBuySellForm;
use alvin0319\BixbyMarket\market\Market;
use alvin0319\BixbyMarket\shop\Shop;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use onebone\economyapi\EconomyAPI;
use pocketmine\block\BlockLegacyIds as BlockIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class EditCategoryInventory{

    private InvMenu $menu;

    public function __construct(private Player $player, private ?Shop $shop = null){
        $this->menu = InvMenu::create(InvMenu::TYPE_CHEST);
        $this->menu->setName("카테고리 수정");
        $this->menu->setListener(\Closure::fromCallable([$this, "onTransaction"]));
        $this->menu->setInventoryCloseListener(\Closure::fromCallable([$this, "onClose"]));

        $this->settingCategory();
    }

    public function onTransaction(InvMenuTransaction $action) : InvMenuTransactionResult{
        return $action->continue();
    }

    public function onClose() : void{
        $res = [];
        foreach ($this->menu->getInventory()->getContents(false) as $index => $item) {
            if ($item->getNamedTag()->getTag("category") !== null) {
                $category = $this->shop->getCategory($item->getNamedTag()->getTag("category")->getValue());
                if ($category !== null) {
                    $res[$index] = $category;
                }
            }
        }
        $this->shop->setCategories($res);
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