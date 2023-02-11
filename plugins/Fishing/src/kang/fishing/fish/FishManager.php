<?php

declare(strict_types=1);

namespace kang\fishing\fish;

use kang\fishing\fish\Fish;
use kang\fishing\Fishing;
use kang\ServerUtils\ServerUtils;
use onebone\economyapi\EconomyAPI;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class FishManager{

    public const ITEMS = [
        ItemIds::PUFFERFISH,
        ItemIds::SALMON,
        ItemIds::CLOWNFISH,
        ItemIds::FISH,
        ItemIds::COOKED_SALMON,
        ItemIds::COOKED_FISH
    ];

    public const FISH_TYPE = [
        "도미",
        "우럭",
        "잉어",
        "피라미",
        "연어",
        "가오리",
        "멸치",
        "장어",
        "메기",
        "개복치",
        "가자미",
        "복어",
        "송어",
        "문어",
        "참치",
        "해삼",
        "상어",
        "오징어",
        "가재",
        "조개",
        "게",
        "새우",
        "홍합",
        "굴",
        "참돔",
        "광어"
    ];

    public const LENGTH_TYPE_D = "D";
    public const LENGTH_TYPE_C = "C";
    public const LENGTH_TYPE_B = "B";
    public const LENGTH_TYPE_A = "A";
    public const LENGTH_TYPE_S = "S";
    public const LENGTH_TYPE_SS = "SS";

    public const LENGTH_TYPE_LIST = [
        self::LENGTH_TYPE_D,
        self::LENGTH_TYPE_C,
        self::LENGTH_TYPE_B,
        self::LENGTH_TYPE_A,
        self::LENGTH_TYPE_S,
        self::LENGTH_TYPE_SS,
    ];

    public const LENGTH_TYPE_COLOR_LIST = [
        self::LENGTH_TYPE_D => "D",
        self::LENGTH_TYPE_C => "§aC",
        self::LENGTH_TYPE_B => "§bB",
        self::LENGTH_TYPE_A => "§dA",
        self::LENGTH_TYPE_S => "§4S",
        self::LENGTH_TYPE_SS => "§eSS",
    ];

    public const LENGTH_TYPE_MIN = [
        1 => self::LENGTH_TYPE_D,
        1000 => self::LENGTH_TYPE_C,
        2000 => self::LENGTH_TYPE_B,
        3000 => self::LENGTH_TYPE_A,
        5000 => self::LENGTH_TYPE_S,
        9000 => self::LENGTH_TYPE_SS,
    ];

    public const PRICE_TYPE = [
        self::LENGTH_TYPE_D => 100,
        self::LENGTH_TYPE_C => 300,
        self::LENGTH_TYPE_B => 500,
        self::LENGTH_TYPE_A => 1000,
        self::LENGTH_TYPE_S => 3000,
        self::LENGTH_TYPE_SS => 5000
    ];

    public const LENGTH_MIN = 1;
    public const LENGTH_MAX = 9999;

    protected array $fishes = [];

    public function __construct(protected Fishing $plugin){
        if ( ! is_dir( $plugin->getDataFolder()."fishes" ) ){
            mkdir($plugin->getDataFolder()."fishes");
        }
        foreach(array_diff(scandir($plugin->getDataFolder() . "fishes/"), [".", ".."]) as $file){
            $content = file_get_contents($plugin->getDataFolder() . "fishes/".$file);
            $playerData = json_decode($content, true);

            $playerName = $playerData["playername"];
            foreach ( $playerData["fishes"] ?? [] as $fishData ){
                $this->fishes[$playerName][] = Fish::jsonDeserialize($fishData);
            }
        }
    }

    public function createFish(Player $player) : Fish{

        $name = self::FISH_TYPE[array_rand(self::FISH_TYPE)];
        $id = self::ITEMS[array_rand(self::ITEMS)];
        $length = mt_rand(self::LENGTH_MIN,self::LENGTH_MAX);

        $type = self::LENGTH_TYPE_D;
        foreach ( self::LENGTH_TYPE_MIN as $minLength => $lengthType ){
            if ( $length < $minLength ){
                $type = self::LENGTH_TYPE_LIST[array_search($lengthType, self::LENGTH_TYPE_LIST)-1] ?? self::LENGTH_TYPE_D;
                break;
            }
        }

        return new Fish($name, $type, $length, $id, $player->getName());
    }

    public function addFish(Player $player, Fish $fish){
        $this->fishes[$player->getName()][] = $fish;
    }

    public function deleteFish(Player $player, int $index) : void{
        if ( isset ( $this->fishes[$player->getName()][$index] ) ){
            unset($this->fishes[$player->getName()][$index]);
        }
    }

    public function getFishByIndex(Player $player, int $index) : ?Fish{
        return $this->fishes[$player->getName()][$index] ?? null;
    }

    public function getFishesByType(Player $player, string $type) : array{
        return array_filter($this->getFishes($player), function($fish) use($type){
            if ( $fish instanceof Fish ){
                return $fish->getType() == $type;
            }
            return false;
        });
    }

    public function getFishes(Player $player) : array{
        $this->fishes[$player->getName()] = array_values($this->fishes[$player->getName()]);
        return $this->fishes[$player->getName()] ?? [];
    }

    public function getFishPrice(Fish $fish) : int{
        return self::PRICE_TYPE[$fish->getType()] ?? 0;
    }

    public function getAllFish(Player $player) : void{
        $empty = false;
        $c = 0;
        foreach ($this->getFishes($player) as $index => $fish) {
            if ($fish instanceof Fish) {
                if ($player->getInventory()->firstEmpty() == -1) {
                    $empty = true;
                    break;
                }
                $player->getInventory()->addItem($fish->asItem());
                $this->deleteFish($player, $index);
                $c++;
            }
        }
        ServerUtils::msg($player, "총 ".$c."마리의 물고기를 가져왔어요.");
        if($empty){
            ServerUtils::msg($player, "인벤토리가 부족하여 나머지는 가져오지 못했어요.");
        }
    }

    public function sellAllFish(Player $player) : void{
        $price = 0;
        $res = [];
        foreach ($this->getFishes($player) as $index => $fish) {
            if ($fish instanceof Fish) {
                $res[$fish->getType()] = ($res[$fish->getType()] ?? 0) + 1;
                $price += self::PRICE_TYPE[$fish->getType()] ?? 0;
            }
        }
        EconomyAPI::getInstance()->addMoney($player, $price);
        ServerUtils::msg($player, "판매 전체 결과: " . implode(", ", array_map(function(string $type, int $count) : string{
                return self::LENGTH_TYPE_COLOR_LIST[$type] . " " . $count . "개";
            }, array_keys($res), array_values($res))));
        ServerUtils::msg($player, "얻은 돈: " . EconomyAPI::getInstance()->koreanWonFormat($price));
    }

    public function save() : void{
        foreach ($this->fishes as $playerName => $fishes) {
            $res = ["playername"=>$playerName];
            foreach ( $fishes as $fish ){
                if ( $fish instanceof Fish ){
                    $res["fishes"][] = $fish->jsonSerialize();
                }
            }
            $content = json_encode($res, JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING);
            file_put_contents($this->plugin->getDataFolder() . "fishes/" . $playerName . ".json", $content);
        }
    }

}