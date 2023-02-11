<?php

declare(strict_types=1);

namespace kang\towny\town;

use kang\PositionUtil\PositionUtil;
use kang\towny\setting\Setting;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;

class Town{

    public function __construct(private string $name, private int $x, private int $z,
                                private Position $spawn, private int $increaseLevel,
                                private string $owner, private array $assistant,
                                private array $members, private int $money,
                                private int $tax, private array $donation,
                                private array $notice
    ){

    }

    public function getName() : string{
        return $this->name;
    }

    public function getSize() : int{
        return Setting::DEFAULT_SIZE + $this->increaseLevel * Setting::INCREASE_SIZE;
    }

    public function getIncreasePrice() : int{
        return Setting::INCREASE_PRICE * $this->increaseLevel + 1;
    }

    public function getMaintenance() : int{
        return Setting::MAINTENANCE_PEOPLE_PRICE * count($this->getMembers()) + Setting::MAINTENANCE_INCREASE_PRICE * $this->increaseLevel;
    }

    public function getMinX() : int{
        return $this->x - Setting::DEFAULT_SIZE - $this->increaseLevel * Setting::INCREASE_SIZE;
    }

    public function getMinZ() : int{
        return $this->z - Setting::DEFAULT_SIZE - $this->increaseLevel * Setting::INCREASE_SIZE;
    }

    public function getMaxX() : int{
        return $this->x + Setting::DEFAULT_SIZE + $this->increaseLevel * Setting::INCREASE_SIZE;
    }

    public function getMaxZ() : int{
        return $this->z + Setting::DEFAULT_SIZE + $this->increaseLevel * Setting::INCREASE_SIZE;
    }

    public function getSpawn() : Position{
        return $this->spawn;
    }

    public function getOwner() : string{
        return $this->owner;
    }

    public function getAssistant() : array{
        return $this->assistant;
    }

    public function getMembers() : array{
        return $this->members;
    }

    public function getNormalMembers() : array{
        $normalMembers = [];
        foreach ( array_keys($this->getMembers()) as $playerName ){
            if ( ! $this->isOwner($playerName) && ! $this->isAssistant($playerName) ){
                $normalMembers[] = $playerName;
            }
        }
        return $normalMembers;
    }

    public function getMoney() : int{
        return $this->money;
    }

    public function getTax() : int{
        return $this->tax;
    }

    public function getDonation(string $playerName) : int{
        return $this->donation[$playerName] ?? 0;
    }

    public function getAllDonation() : array{
        return $this->donation;
    }

    public function getAllDonationPrice() : int{
        $total = 0;
        foreach ( $this->getAllDonation() as $playerName => $price ){
            $total+=$price;
        }
        return $total;
    }

    public function getNotice() : array{
        return $this->notice;
    }

    public function isOwner(string $playerName) : bool{
        return strtolower($playerName) == strtolower($this->owner);
    }

    public function isAssistant(string $playerName) : bool{
        return isset($this->assistant[$playerName]);
    }

    public function isMember(string $playerName) : bool{
        return isset($this->members[$playerName]);
    }

    public function increase(){
        $this->increaseLevel += 1;
    }

    public function teleport(Player $player) : void{
        $player->teleport($this->getSpawn());
    }

    public function addAssistant(string $playerName) : void{
        $this->assistant[$playerName] = true;
    }

    public function removeAssistant(string $playerName) : void{
        if ( isset ( $this->assistant[$playerName] ) ){
            unset($this->assistant[$playerName]);
        }
    }

    public function addMember(Player $player) : void{
        $this->members[$player->getName()] = true;
    }

    public function kick(string $playerName) : void{
        if ( isset ( $this->members[$playerName] ) ){
            unset($this->members[$playerName]);
        }
    }

    public function addMoney(int $price){
        $this->money += $price;
    }

    public function reduceMoney(int $price){
        $this->money -= $price;
        if ( $this->money < 0 ) $this->money = 0;
    }

    public function setTax(int $price){
        $this->tax = $price;
    }

    public function donate(string $playerName, int $price){
        $this->donation[$playerName] = ($this->donation[$playerName] ?? 0) + $price;
        $this->addMoney($price);
    }

    public function broadMsg(string $msg){
        $server = Server::getInstance();
        foreach ( array_keys($this->getMembers()) as $playerName ){
            if ( ($player = $server->getPlayerExact($playerName)) !== null ){
                if ( $player instanceof Player ){
                    $player->sendMessage("§e마을 | §r".$msg);
                }
            }
        }
    }

    public function jsonSerialize() : array{
        return [
            "name"=>$this->name,
            "x"=>$this->x,
            "z"=>$this->z,
            "spawn"=>PositionUtil::getStringByPos($this->spawn),
            "increaseLevel"=>$this->increaseLevel,
            "owner"=>$this->owner,
            "assistant"=>$this->assistant,
            "members"=>$this->members,
            "money"=>$this->money,
            "tax"=>$this->tax,
            "donation"=>$this->donation,
            "notice"=>$this->notice
        ];
    }

    public static function jsonDeserialize(array $townData) : Town{
        return new Town(
            $townData["name"],
            $townData["x"],
            $townData["z"],
            PositionUtil::getPosByString($townData["spawn"]),
            $townData["increaseLevel"],
            $townData["owner"],
            $townData["assistant"],
            $townData["members"],
            $townData["money"],
            $townData["tax"],
            $townData["donation"],
            $townData["notice"]
        );
    }

}