<?php

/*
 *       _       _        ___ _____ _  ___
 *   __ _| |_   _(_)_ __  / _ \___ // |/ _ \
 * / _` | \ \ / / | '_ \| | | ||_ \| | (_) |
 * | (_| | |\ V /| | | | | |_| |__) | |\__, |
 *  \__,_|_| \_/ |_|_| |_|\___/____/|_|  /_/
 *
 * Copyright (C) 2019 alvin0319
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Cash;

use kang\dataconfig\DataConfig;
use kang\ServerUtils\ServerUtils;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

class Cash extends PluginBase implements Listener{

    protected static Cash $instance;

	public static string $prefix = "§d<§f시스템§d> §f";

	protected DataConfig $config;
	protected array $db = [];

	public static function getInstance(): Cash{
	    return self::$instance;
    }

    protected function onLoad() : void{
		self::$instance = $this;
	}

	protected function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvents($this, $this);

		$this->config = new DataConfig($this->getDataFolder()."cash.json",DataConfig::TYPE_JSON, [
		    "player" => [],
            "shop" => []
        ]);
		$this->db = $this->config->getAll();

		/*$this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function() : void{
			$this->save();
		}), 1200 * 10);*/
	}

	protected function onDisable() : void{
		$this->save();
	}

	public function save(){
		$this->config->save($this->db);
	}

	public function setCash($player, int $cash){
		$player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
		$this->db["player"] [$player] = $cash;
	}

	public function addCash($player, int $cash){
		$player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
		$this->db["player"] [$player] += $cash;
		$this->save();
	}

	public function getCash($player) : ?int{
		$player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
		return $this->db["player"] [$player] ?? null;
	}

	public function reduceCash($player, int $cash){
		$player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
		$this->db["player"] [$player] -= $cash;
		$this->save();
	}

	public function isExistsData($player) : bool{
		$player = $player instanceof Player ? strtolower($player->getName()) : strtolower($player);
		return isset($this->db["player"] [$player]);
	}

	public function onJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();

		if(!$this->isExistsData($player)){
			$this->setCash($player, 0);
		}
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		switch($args[0] ?? "x"){
			case "정보":
				array_shift($args);
				$name = array_shift($args);
				if(!isset($name)){
					$name = $sender->getName();
				}

				if($this->getCash($name) !== null){
                    ServerUtils::msg($sender, $name . " 님의 캐시: " . $this->koreanWonFormat($this->getCash($name)));
				}else{
                    ServerUtils::error($sender, $name . " 님의 접속 기록을 찾을 수 없어요.");
				}
				break;
			case "주기":
				if(!$sender->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
					break;
				}
				array_shift($args);
				$name = array_shift($args);
				$cash = array_shift($args);
				if(!isset($name)){
                    ServerUtils::msg($sender, "/캐시 주기 [닉네임] [양]");
					break;
				}

				if($this->getCash($name) === null){
                    ServerUtils::error($sender, "해당 플레이어는 서버에 접속한 적이 없습니다.");
					break;
				}

				if(!isset($cash) or !is_numeric($cash)){
                    ServerUtils::error($sender, "지급할 캐시의 양은 숫자여야 해요.");
					break;
				}

				$this->addCash($name, (int) $cash);
                ServerUtils::msg($sender, $name."님에게 캐시를 ".$cash." 만큼 지급했어요.");
				break;
			case "뺏기":
				if(!$sender->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
					break;
				}
				array_shift($args);
				$name = array_shift($args);
				$cash = array_shift($args);
				if(!isset($name)){
                    ServerUtils::msg($sender, "/캐시 뺏기 [닉네임] [양 ");
					break;
				}

				if($this->getCash($name) === null){
                    ServerUtils::error($sender, "해당 플레이어의 접속 기록을 찾을 수 없어요.");
					break;
				}

				if(!isset($cash) or !is_numeric($cash)){
                    ServerUtils::error($sender, "뺏을 캐시의 양은 숫자여야 해요.");
					break;
				}

				$this->reduceCash($name, (int) $cash);
                ServerUtils::msg($sender, $name."님의 캐시를 ".$cash." 만큼 회수했어요.");
				break;
			case "설정":
				if(!$sender->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
					break;
				}
				array_shift($args);
				$name = array_shift($args);
				$cash = array_shift($args);

				if(!isset($name)){
                    ServerUtils::msg($sender, "/캐시 설정 [닉네임] [양]");
					break;
				}

				if($this->getCash($name) === null){
                    ServerUtils::error($sender, "해당 플레이어의 접속 기록을 찾을 수 없어요.");
					break;
				}

				if(!isset($cash) or !is_numeric($cash)){
                    ServerUtils::error($sender, "설정할 캐시의 양은 숫자여야 해요.");
					break;
				}

				$this->setCash($name, (int) $cash);
				ServerUtils::msg($sender, $name."님의 캐시를 ".$cash." (으)로 설정했어요.");
				break;
			default:
                ServerUtils::msg($sender, "/캐시 정보 [빈칸|닉네임] - 캐시 정보를 확인해요.");
                ServerUtils::msg($sender, "/캐시 상점 - 캐시상점을 엽니다.");
				if($sender->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
					foreach([
						["/캐시 주기 [닉네임] [양]", "캐시를 지급합니다."],
						["/캐시 뺏기 [닉네임] [양]", "캐시를 뺏습니다."],
                        ["/캐시 설정 [닉네임] [양]", "캐시를 설정합니다."]
					] as $usage){
                        ServerUtils::msg($sender, $usage[0] . " - " . $usage[1]);
					}
				}
		}
		return true;
	}

	public function koreanWonFormat(int $money) : string{
		$elements = [];
		if($money >= 1000000000000){
			$elements[] = floor($money / 1000000000000) . "조";
			$money %= 1000000000000;
		}
		if($money >= 100000000){
			$elements[] = floor($money / 100000000) . "억";
			$money %= 100000000;
		}
		if($money >= 10000){
			$elements[] = floor($money / 10000) . "만";
			$money %= 10000;
		}
		if(count($elements) == 0 || $money > 0){
			$elements[] = $money;
		}
		return implode(" ", $elements) . "캐시";
	}

    public function getMonetaryUnit() : string{
        return "§6§lC§r";
    }

}