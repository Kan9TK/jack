<?php

declare(strict_types=1);

namespace kang\fishing\inventory;

use kang\fishing\Fishing;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\player\Player;

class RewardInventory{

    protected InvMenu $menu;

    public function __construct(){
        $this->menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $this->menu->setName("낚시 보상");
        $this->menu->setListener(\Closure::fromCallable([$this, "onTransaction"]));
        $this->menu->setInventoryCloseListener(\Closure::fromCallable([$this, "onClose"]));

        foreach ( Fishing::getInstance()->getContestManager()->getRewards() as $index => $nbt ){
            $item = Fishing::getInstance()->getContestManager()->getReward($index);
            $this->menu->getInventory()->addItem($item);
        }
    }

    public function onTransaction(InvMenuTransaction $action) : InvMenuTransactionResult{
        return $action->continue();
    }

    public function onClose() : void{
        $res = [];
        foreach ( $this->menu->getInventory()->getContents() as $index => $content ){
            $res[$index] = $content->jsonSerialize();
        }
        Fishing::getInstance()->getContestManager()->setRewards($res);
    }

    public function sendTo(Player $player){
        $this->menu->send($player);
    }

}