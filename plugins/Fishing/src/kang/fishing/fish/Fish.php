<?php

declare(strict_types=1);

namespace kang\fishing\fish;

use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\utils\TextFormat;

class Fish{

    public function __construct(protected string $name, protected string $type, protected int $length, protected int $id, protected string $owner){
    }

    public function getName() : string{
        return $this->name;
    }

    public function getType() : string{
        return $this->type;
    }

    public function getLength() : int{
        return $this->length;
    }

    public function getId() : int{
        return $this->id;
    }

    public function getOwner() : string{
        return $this->owner;
    }

    public function getCustomName() : string{
        return FishManager::LENGTH_TYPE_COLOR_LIST[$this->type].TextFormat::RESET."등급 ".$this->name." (".$this->length."cm)";
    }

    public function asItem() : Item{
        $item = ItemFactory::getInstance()->get($this->id);
        $item->setCustomName(TextFormat::RESET.$this->name);
        $item->setLore([
            TextFormat::RESET."등급: ".FishManager::LENGTH_TYPE_COLOR_LIST[$this->type],
            TextFormat::RESET."길이: ".$this->length."cm",
            "",
            TextFormat::RESET."주인: ".$this->owner
        ]);
        return $item;
    }

    public function jsonSerialize() : array{
        return [
            "name"=>$this->name,
            "type"=>$this->type,
            "length"=>$this->length,
            "id"=>$this->id,
            "owner"=>$this->owner
        ];
    }

    public static function jsonDeserialize($fishData) : Fish{
        return new self(
            $fishData["name"],
            $fishData["type"],
            $fishData["length"],
            $fishData["id"],
            $fishData["owner"]
        );
    }

}