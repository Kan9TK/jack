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

class MarketInventory{

    protected InvMenu $menu;

    protected int $pages = 0;
    protected int $page = 0;

    public function __construct(private Player $player, private Shop $shop, private Category $category){
        $this->menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $this->menu->setName($shop->getName());
        $this->menu->setListener(\Closure::fromCallable([$this, "onTransaction"]));
        $this->menu->setInventoryCloseListener(\Closure::fromCallable([$this, "onClose"]));


        $markets = $this->category->getMarkets();
        $lastIndex = array_key_last($markets);
        $this->pages = (int)($lastIndex / 2) - 1;

        $this->setting();

    }

    public function onTransaction(InvMenuTransaction $action) : InvMenuTransactionResult
    {

        $item = $action->getOut();

        if ($item->getNamedTag()->getTag("previous") !== null) {
            if ($this->page > 0 ) {
                $this->page--;
                $this->setting();
            }
            return $action->discard();
        }

        if ($item->getNamedTag()->getTag("next") !== null) {
            if (
                $this->pages > 0
                &&
                $this->page < $this->pages
            ) {
                $this->page++;
                $this->setting();
            }
            return $action->discard();
        }

        if ($item->getNamedTag()->getTag("market") !== null) {
            $market = BixbyMarket::getInstance()->getMarketManager()->getMarketById($item->getNamedTag()->getTag("market")->getValue());
            if ($market === null) {
                return $action->discard();
            }
            $action->getPlayer()->removeCurrentWindow();
            return $action->discard()->then(function (Player $p) use ($market): void {
                $p->sendForm(new MarketBuySellForm($p, $this->shop, $market));
            });
        }
        if ($item->getNamedTag()->getTag("sellall") !== null) {
            $this->category->sellAll($action->getPlayer(), $this->shop->getEconomyProvider());
        }
        return $action->discard();

    }

    public function onClose() : void{}

    public function setting() : void{
        $this->menu->getInventory()->clearAll();
        $this->settingItems();
        $this->settingButtons();
    }

    public function settingButtons() : void{
        $item = ItemFactory::getInstance()->get(BlockIds::BED_BLOCK, 0, 1)
            ->setCustomName("§r전체 판매");
        $item->getNamedTag()->setString("sellall", "");
        $this->menu->getInventory()->setItem(53, $item);

        $item = ItemFactory::getInstance()->get(ItemIds::PAPER, 0, 1)
            ->setCustomName("§r이전 페이지")
            ->setLore(["§r현재 페이지: ".($this->page+1)."/".($this->pages+1)]);
        $item->getNamedTag()->setString("previous", "");
        $this->menu->getInventory()->setItem(48, $item);

        $item = VanillaBlocks::CHEST()->asItem()
            ->setCustomName("§r잔고")
            ->setLore(["§r내 잔고: ".EconomyAPI::getInstance()->koreanWonFormat($this->shop->getEconomyProvider()->getMoney($this->player)).$this->shop->getEconomyProvider()->getUnit()]);
        $item->getNamedTag()->setString("info", "");
        $this->menu->getInventory()->setItem(49, $item);

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

                        $unit = $this->shop->getEconomyProvider()->getUnit();
                        $buyPrice = $market->getBuyPrice() >= 0 ? EconomyAPI::getInstance()->koreanWonFormat($market->getBuyPrice()) . $unit : "§c구매 불가";
                        $sellPrice = $market->getSellPrice() >= 0 ? EconomyAPI::getInstance()->koreanWonFormat($market->getSellPrice()) . $unit : "§c판매 불가";

                        $lore = $item->getLore();
                        $lore[] = "";
                        $lore[] = "§r§8―――――――――――――";
                        $lore[] = "§r§b구매가: §f" . $buyPrice;
                        $lore[] = "§r§d판매가: §f" . $sellPrice;
                        $lore[] = "§r§8―――――――――――――";

                        $item->setLore($lore);
                        $item->getNamedTag()->setInt("market", $market->getId());

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