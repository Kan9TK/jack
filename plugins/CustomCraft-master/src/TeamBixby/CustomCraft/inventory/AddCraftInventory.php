<?php

declare(strict_types=1);

namespace TeamBixby\CustomCraft\inventory;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use TeamBixby\CustomCraft\CustomCraft;

class AddCraftInventory{

    protected InvMenu $menu;
    protected Player $player;

    public function __construct(){
        $this->menu = InvMenu::create(Invmenu::TYPE_DOUBLE_CHEST);
        $this->menu->setName("Add crafting recipe");
        $this->menu->setListener(\Closure::fromCallable([$this, "onTransaction"]));
        $this->menu->setInventoryCloseListener(\Closure::fromCallable([$this, "onClose"]));

        $border = ItemFactory::getInstance()->get(ItemIds::IRON_BARS);
        $border->getNamedTag()->setByte("border", (int) true);
        $border->setCustomName("§l ");

        $craftSlots = [19, 20, 21, 28, 29, 30, 37, 38, 39];
        $resultSlots = 33;
        for($i = 0; $i < $this->menu->getInventory()->getSize(); $i++){
            if(!in_array($i, array_merge($craftSlots, [$resultSlots]))){
                $this->menu->getInventory()->setItem($i, $border);
            }
        }
    }

    public function onTransaction(InvMenuTransaction $action) : InvMenuTransactionResult{
        $item = $action->getOut();
        $return = $action->discard();

        if($item->getNamedTag()->getTag("border") !== null){
            return $return;
        }
        return $action->continue();
    }

    public function onClose() : void{
        $allEmpty = true;
        $craftSlots = [19, 20, 21, 28, 29, 30, 37, 38, 39];
        $resultSlots = 33;
        foreach($craftSlots as $slot){
            if(!$this->menu->getInventory()->getItem($slot)->isNull()){
                $allEmpty = false;
                break;
            }
        }
        if($allEmpty){
            $this->player->sendMessage("You must provide the recipe.");
            return;
        }
        if($this->menu->getInventory()->getItem($resultSlots)->isNull()){
            $this->player->sendMessage("You must provide the result item.");
            return;
        }
        $a = "A";
        $res = [];
        foreach($craftSlots as $slot){
            $res[$a] = $this->menu->getInventory()->getItem($slot);
            $a++;
        }
        CustomCraft::getInstance()->registerShapedRecipe($res, $this->menu->getInventory()->getItem($resultSlots));
        $this->player->sendMessage("Success");
    }

    public function sendTo(Player $player) : void{
        $this->player = $player;
        $this->menu->send($player);
    }

}