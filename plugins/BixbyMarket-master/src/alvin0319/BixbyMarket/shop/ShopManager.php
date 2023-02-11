<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\shop;

use alvin0319\BixbyMarket\BixbyMarket;
use alvin0319\BixbyMarket\category\Category;
use alvin0319\BixbyMarket\inventory\MarketInventory;
use alvin0319\BixbyMarket\inventory\SelectCategoryInventory;
use pocketmine\player\Player;

class ShopManager{

    protected array $shops = [];

    public function __construct(){
        if(!file_exists($file = BixbyMarket::getInstance()->getDataFolder() . "shops.json")){
            return;
        }
        $data = json_decode(file_get_contents($file), true);
        foreach($data as $shopData){
            $shop = Shop::jsonDeserialize($shopData);
            $this->shops[$shop->getName()] = $shop;
        }
    }

    public function addShop(Shop $shop) : void{
        $this->shops[$shop->getName()] = $shop;
    }

    public function deleteShop(Shop $shop) : void{
        if(isset($this->shops[$shop->getName()])) {
            unset($this->shops[$shop->getName()]);
        }
    }

    public function getShop(string $name) : ?Shop{
        return $this->shops[$name] ?? null;
    }

    public function getShops() : array{
        return $this->shops;
    }

    public function save() : void{
        $res = [];
        foreach($this->shops as $name => $shop){
            $res[] = $shop->jsonSerialize();
        }
        file_put_contents(BixbyMarket::getInstance()->getDataFolder() . "shops.json", json_encode($res, JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING));
    }

}