<?php

declare(strict_types=1);

namespace TeamBixby\CustomCraft\inventory;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use TeamBixby\CustomCraft\CustomCraft;

class ListCraftInventory{

    protected InvMenu $menu;
    protected Player $player;
    protected int $page = 0;
    protected int $recipe = 0;

    public function __construct(){
        $this->menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $this->menu->setName("조합 목록");
        $this->menu->setListener(\Closure::fromCallable([$this, "onTransaction"]));
        $this->menu->setInventoryCloseListener(\Closure::fromCallable([$this, "onClose"]));

        $craftSlots = [19, 20, 21, 28, 29, 30, 37, 38, 39];
        $resultSlots = 33;

        $this->settingOutput();
    }

    public function settingOutput(){

        $this->menu->getInventory()->clearAll();

        foreach ( CustomCraft::getInstance()->getAllShapedRecipe() as $index => $craftData ){
            if ( $index >= $this->page*45 && $index <= ($this->page+1)*45 ) {
                $output = Item::jsonDeserialize($craftData["output"]);
                $output->getNamedTag()->setString("craft", "");
                $this->menu->getInventory()->addItem($output);
            }
        }

        $item = ItemFactory::getInstance()->get(ItemIds::PAPER, 0, 1)
            ->setCustomName("§r이전 페이지")
            ->setLore(["§r현재 페이지: ".($this->page+1)]);
        $item->getNamedTag()->setString("previous", "");
        $this->menu->getInventory()->setItem(48, $item);

        $item = ItemFactory::getInstance()->get(ItemIds::PAPER, 0, 1)
            ->setCustomName("§r다음 페이지")
            ->setLore(["§r현재 페이지: ".($this->page+1)]);
        $item->getNamedTag()->setString("next", "");
        $this->menu->getInventory()->setItem(50, $item);
        
    }

    public function settingRecipe(int $slot){

        $this->menu->getInventory()->clearAll();

        $this->recipe = $slot;

        $border = ItemFactory::getInstance()->get(ItemIds::IRON_BARS);
        $border->getNamedTag()->setByte("border", (int) true);
        $border->setCustomName("§l ");
        $craftSlots = [19, 20, 21, 28, 29, 30, 37, 38, 39];
        $resultSlots = 33;
        $getSlots = 52;
        $backSlots = 53;
        for($i = 0; $i < $this->menu->getInventory()->getSize(); $i++){
            if(!in_array($i, array_merge($craftSlots, [$resultSlots], [$backSlots], [$getSlots]))){
                $this->menu->getInventory()->setItem($i, $border);
            }
        }

        $craftData = CustomCraft::getInstance()->getAllShapedRecipe()[$slot+($this->page*45)];
        [$a, $b, $c, $d, $e, $f, $g, $h, $i] = array_map(function(array $data) : Item{
            return Item::jsonDeserialize($data);
        }, $craftData["input"]);
        $output = Item::jsonDeserialize($craftData["output"]);

        $j = 0;
        foreach ( $craftSlots as $slot ){
            $item = [$a, $b, $c, $d, $e, $f, $g, $h, $i][$j];
            $this->menu->getInventory()->setItem($slot, $item);
            $j++;
        }
        $this->menu->getInventory()->setItem($resultSlots, $output);

        $back = ItemFactory::getInstance()->get(ItemIds::BED_BLOCK);
        $back->getNamedTag()->setByte("give", (int) true);
        $back->setCustomName("§r아이템 지급");
        $this->menu->getInventory()->setItem($getSlots, $back);

        $back = ItemFactory::getInstance()->get(ItemIds::BED_BLOCK);
        $back->getNamedTag()->setByte("back", (int) true);
        $back->setCustomName("§r뒤로 가기");
        $this->menu->getInventory()->setItem($backSlots, $back);

    }

    public function giveItems(int $slot) : void{
        $craftData = CustomCraft::getInstance()->getAllShapedRecipe()[$this->recipe];
        [$a, $b, $c, $d, $e, $f, $g, $h, $i] = array_map(function(array $data) : Item{
            return Item::jsonDeserialize($data);
        }, $craftData["input"]);
        $output = Item::jsonDeserialize($craftData["output"]);
        foreach ( [$a, $b, $c, $d, $e, $f, $g, $h, $i] as $item ){
            $this->player->getInventory()->addItem($item);
        }
        $this->player->getInventory()->addItem($output);
    }

    public function onTransaction(InvMenuTransaction $action) : InvMenuTransactionResult{
        $item = $action->getOut();
        if ( $item->getNamedTag()->getTag("craft") !== null ){
            $this->settingRecipe($action->getAction()->getSlot());
        }
        elseif ( $item->getNamedTag()->getTag("give") !== null ){
            $this->giveItems($action->getAction()->getSlot());
        }
        elseif ( $item->getNamedTag()->getTag("back") !== null ){
            $this->settingOutput();
        }
        elseif ( $item->getNamedTag()->getTag("previous") !== null ){
            if ( $this->page > 0 ){
                $this->page--;
                $this->settingOutput();
            }
        }
        elseif ( $item->getNamedTag()->getTag("next") !== null ){
            $this->page++;
            $this->settingOutput();
        }
        return $action->discard();
    }

    public function onClose() : void{

    }

    public function sendTo(Player $player) : void{
        $this->player = $player;
        $this->menu->send($player);
    }

}