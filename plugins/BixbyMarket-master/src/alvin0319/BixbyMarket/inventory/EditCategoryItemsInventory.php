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

class EditCategoryItemsInventory{

    protected InvMenu $menu;

    protected int $pages = 0;
    protected int $page = 0;

    public function __construct(protected Player $player, protected Shop $shop, protected Category $category){
        $this->menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $this->menu->setName("품목 수정");
        $this->menu->setListener(\Closure::fromCallable([$this, "onTransaction"]));
        $this->menu->setInventoryCloseListener(\Closure::fromCallable([$this, "onClose"]));

        $this->setting();
    }

    public function onTransaction(InvMenuTransaction $action) : InvMenuTransactionResult{

        $item = $action->getOut();

        if ($item->getNamedTag()->getTag("previous") !== null) {
            if ($this->page > 0) {
                $this->page--;
                $this->setting();
            }
            return $action->discard();
        }

        if ($item->getNamedTag()->getTag("next") !== null) {
            $this->save();
            $this->page++;
            $this->setting();
            return $action->discard();
        }

        return $action->continue();
    }

    public function save() : void{
        $res = [];
        foreach ($this->menu->getInventory()->getContents(false) as $index => $item) {
            if ($item->getNamedTag()->getTag("previous") == null && $item->getNamedTag()->getTag("next") == null) {
                $market = BixbyMarket::getInstance()->getMarketManager()->getMarketByItem($item);
                if ($market === null) {
                    $market = BixbyMarket::getInstance()->getMarketManager()->registerMarket($item, -1, -1);
                }
                $res[$index + ($this->page * 45)] = $market;
            }
            $this->category->setMarkets($res);
        }
    }

    public function onClose() : void
    {
        $this->save();
    }

    public function setting() : void{
        $this->menu->getInventory()->clearAll();
        $this->settingItems();
        $this->settingButtons();
    }

    public function settingButtons() : void{
        $item = ItemFactory::getInstance()->get(ItemIds::PAPER, 0, 1)
            ->setCustomName("§r이전 페이지")
            ->setLore(["§r현재 페이지: ".($this->page+1)."/".($this->pages+1)]);
        $item->getNamedTag()->setString("previous", "");
        $this->menu->getInventory()->setItem(48, $item);

        $item = ItemFactory::getInstance()->get(ItemIds::PAPER, 0, 1)
            ->setCustomName("§r다음 페이지")
            ->setLore(["§r현재 페이지: ".($this->page+1)."/".($this->pages+1)]);
        $item->getNamedTag()->setString("next", "");
        $this->menu->getInventory()->setItem(50, $item);
    }

    public function settingItems(){

        if ($this->category instanceof Category) {

            foreach ($this->category->getMarkets() as $index => $market) {

                if ($market instanceof Market) {

                    if ($index >= $this->page * 45 && $index <= ($this->page + 1) * 45) {

                        $item = $market->getItem();
                        $this->menu->getInventory()->setItem($index - $this->page * 45, $item);

                    }

                }
            }
        }

    }

    public function send() : void{
        $this->menu->send($this->player);
    }

}