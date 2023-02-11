<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\shop;

use alvin0319\BixbyMarket\BixbyMarket;
use alvin0319\BixbyMarket\category\Category;
use alvin0319\BixbyMarket\economy\EconomyProvider;
use pocketmine\item\VanillaItems;

class Shop{

    public const DEFAULT_INDEX = 0;

    public function __construct(private string $name, private EconomyProvider $economyProvider, private array $categories){
    }

    public function getName() : string{
        return $this->name;
    }

    public function addCategory(int $index, Category $category) : void{
        $this->categories[$index] = $category;
    }

    public function removeCategory(Category $category) : void{
    }

    public function getCategory(string $name) : ?Category{
        foreach($this->categories as $index => $category){
            if($category->getName() === $name){
                return $category;
            }
        }
        return null;
    }

    public function getDefaultCategory() : Category{
        return $this->categories[self::DEFAULT_INDEX];
    }

    public function getCategories() : array{
        return $this->categories;
    }

    public function setCategories(array $categories) : void{
        $this->categories = $categories;
    }

    public function getEconomyProvider() : EconomyProvider{
        return $this->economyProvider;
    }

    public function getAvailableIndex() : ?int{
        for($i = 0; $i < 54; $i++){
            if(!isset($this->categories[$i])){
                return $i;
            }
        }
        return null;
    }

    public function jsonSerialize() : array{
        $categories = [];
        foreach($this->categories as $index => $category){
            if ( $category instanceof Category ){
                $categories[$index] = $category->jsonSerialize();
            }
        }
        return [
            "name" => $this->name,
            "economyprovider" => $this->economyProvider::getName(),
            "categories" => $categories,
        ];
    }

    public static function jsonDeserialize(array $data) : Shop{
        $categories = [];
        foreach($data["categories"] as $index => $category){
            $categories[$index] = Category::jsonDeserialize($category);
        }
        return new Shop(
            $data["name"],
            BixbyMarket::getInstance()->getEconomyProviderManager()->getProvider($data["economyprovider"]),
            $categories
        );
    }

}