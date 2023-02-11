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

class AddFurnaceInventory{

    protected InvMenu $menu;
    protected Player $player;

    public function __construct(){
        $this->menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $this->menu->setName("Add furnace recipe");
        $this->menu->setListener(\Closure::fromCallable([$this, "onTransaction"]));
        $this->menu->setInventoryCloseListener(\Closure::fromCallable([$this, "onClose"]));

        $inputSlot = 11;
        $outputSlot = 15;
        $borderSlot = 13;

        $border = ItemFactory::getInstance()->get(ItemIds::IRON_BARS);

        if ( $border instanceof Item ) {
            $border->getNamedTag()->setByte("border", (int)true);
            $border->setCustomName("§l ");
            for ($i = 0; $i < $this->menu->getInventory()->getSize(); $i++) {
                if ($i !== $inputSlot && $i !== $outputSlot && $i !== $borderSlot) {
                    $this->menu->getInventory()->setItem($i, $border);
                }
            }
            $border = ItemFactory::getInstance()->get(-161)->setCustomName("§l ");
            $border->getNamedTag()->setByte("border", (int)true);
        }
        $this->menu->getInventory()->setItem($borderSlot, $border);
    }

    public function onTransaction(InvMenuTransaction $action) : InvMenuTransactionResult{
        $discard = $action->discard();
        $item = $action->getOut();

        if ( $item instanceof Item ) {
            if ($item->getNamedTag()->getTag("border") !== null) {
                return $discard;
            }
        }

        return $action->continue();
    }

    public function onClose() : void{

        $inputSlot = 11;
        $outputSlot = 15;
        $borderSlot = 13;

        $input = $this->menu->getInventory()->getItem($inputSlot);
        $output = $this->menu->getInventory()->getItem($outputSlot);

        if($input->isNull() || $output->isNull()){
            $this->player->sendMessage("You must provide the input and output.");
            return;
        }
        CustomCraft::getInstance()->registerFurnaceRecipe($input, $output);
        $this->player->sendMessage("Success");
    }

    public function sendTo(Player $player) : void{
        $this->player = $player;
        $this->sendTo($player);
    }

}