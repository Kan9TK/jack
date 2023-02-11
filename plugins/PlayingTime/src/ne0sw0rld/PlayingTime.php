<?php

namespace ne0sw0rld;

use ne0sw0rld\listener\EventListener;
use pocketmine\plugin\PluginBase;

use pocketmine\player\Player;
use pocketmine\Server;

use ne0sw0rld\command\TimeRankC;
use ne0sw0rld\command\TimeCheckC;
use kang\dataconfig\DataConfig;
use kang\ServerUtils\ServerUtils;

use function kang\ServerUtils\convert;

class PlayingTime extends PluginBase{

    protected static PlayingTime $instance;

	public DataConfig $playerC;
    public array $playerD;

	public array $time = [];

	public static function getInstance() : PlayingTime{
	    return self::$instance;
    }

	protected function onLoad(): void{
        self::$instance = $this;
    }

    public function onEnable () : void{

		$this->playerC = new DataConfig($this->getDataFolder()."playingTime.json");
		$this->playerD = $this->playerC->data;

		$this->getServer()->getCommandMap()->registerAll($this->getName(), [
		    new TimeCheckC($this),
            new TimeRankC($this)
        ]);

		$this->getServer()->getPluginManager()->registerEvents (new EventListener($this), $this);

    }
	
	public function onDisable () : void{
		$this->updateAllPlayers ();
		$this->playerC->save($this->playerD);
	}

	public function updateTime ($player){
		
		$name = convert($player);

		if ( ! isset ( $this->time[$name] )){
            return;
        }

		$time = $this->time[$name];
        if ( ! isset ( $this->playerD[$name] ) ){
            $this->playerD[$name] = time() - $time;
            return;
        }

        $this->playerD[$name] += (time() - $time);
        $this->time[$name] = time();
	}
	
	public function updateAllPlayers() : array{
		foreach ($this->time as $player => $time) {
			$this->updateTime($player);
		}
		return $this->playerD;
	}
	
	public function getTime ($player) : int{
		$name =	$player instanceof Player ? strtolower ($player->getName()) : strtolower ($player);
		$this->updateTime ($player);
		return $this->playerD[$name] ?? 0;
	}
	
	public function getKoreanTime ($player) : string{
		return $this->koreanTimeFormat(($this->getTime($player)));
	}
	
	public function koreanTimeFormat (int $time, string $h = '시간', string $m = '분', string $s = '초') : string{

		$hour = floor ($time / 60 / 60);
		$minute = floor ($time / 60 - ($hour * 60));
		$second = $time - ($hour * 60 * 60 + $minute * 60);

		return "{$hour}{$h} {$minute}{$m} {$second}{$s}";

	}


}