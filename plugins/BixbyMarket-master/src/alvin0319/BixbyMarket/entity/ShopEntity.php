<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\entity;

use alvin0319\BixbyMarket\BixbyMarket;
use alvin0319\BixbyMarket\inventory\MarketInventory;
use alvin0319\BixbyMarket\inventory\SelectCategoryInventory;
use alvin0319\BixbyMarket\shop\Shop;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;

class ShopEntity extends CustomHuman{

    protected string $shopName;

    protected function initEntity(CompoundTag $nbt) : void{
        $this->shopName = $nbt->getTag("shop")->getValue();
        parent::initEntity($nbt);
        $this->setNameTagVisible(true);
        $this->setNameTagAlwaysVisible(true);
    }

    public function saveNBT() : CompoundTag{
        $nbt = parent::saveNBT();
        $nbt->setString("shop", $this->shopName);
        return $nbt;
    }

    public function attack(EntityDamageEvent $source) : void{
        if($source instanceof EntityDamageByEntityEvent){
            $damager = $source->getDamager();
            if ( $damager instanceof Player ){

                if ( $damager->isSneaking() && $damager->hasPermission(DefaultPermissions::ROOT_OPERATOR) ){
                    $this->close();
                    return;
                }

                $shop = BixbyMarket::getInstance()->getShopManager()->getShop($this->shopName);
                if ( $shop instanceof Shop ){
                    $inv = new SelectCategoryInventory($damager, $shop);
                    $inv->send();
                }

            }
            $source->cancel();
        }
    }

}