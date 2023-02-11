<?php

declare(strict_types=1);

namespace kang\fishing\session;

use kang\fishing\projectile\FishingHook;
use kang\fishing\Fishing;
use pocketmine\player\Player;

class SessionManager{

    protected array $sessions = [];

    public function __construct(protected Fishing $plugin){
    }

    public function startFishing(Player $player, FishingHook $fishingHook) : void{
        $this->sessions[$player->getName()]["fishing"] = $fishingHook;
    }

    public function getFishingHook(Player $player) : ?FishingHook{
        if(isset($this->sessions[$player->getName()]["fishing"])){
            $fishigHook = $this->sessions[$player->getName()]["fishing"];
            if ( ! $fishigHook instanceof FishingHook ){
                unset($this->sessions[$player->getName()]["fishing"]);
                return null;
            }
        }
        return $this->sessions[$player->getName()]["fishing"] ?? null;
    }

    public function finishFishing(?Player $player) : void{
        if(!$player instanceof Player) return;
        if(isset($this->sessions[$player->getName()]["fishing"])){
            unset($this->sessions[$player->getName()]["fishing"]);
        }
    }

    public function setRanking(Player $player, int $ranking) : void{
        $this->sessions[$player->getName()]["ranking"] = $ranking;
    }

    public function getRanking(Player $player) : int{
        return $this->sessions[$player->getName()]["ranking"] ?? 999;
    }

    public function resetRanking() : void{
        foreach ( $this->sessions as $playerName => $session ){
            $this->sessions[$playerName]["ranking"] = 999;
        }
    }

}