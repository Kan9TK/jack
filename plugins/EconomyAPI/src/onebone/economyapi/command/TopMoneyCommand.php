<?php

/*
 * EconomyS, the massive economy plugin with many features for PocketMine-MP
 * Copyright (C) 2013-2016  onebone <jyc00410@gmail.com>
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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace onebone\economyapi\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use onebone\economyapi\EconomyAPI;
use kang\ServerUtils\ServerUtils;

class TopMoneyCommand extends Command{

	private $plugin;

	public function __construct(EconomyAPI $plugin){
		parent::__construct("돈순위", "돈 순위를 표시합니다.", "/돈순위 [페이지]", ["topmoney"]);
		$this->setPermission("economyapi.command.topmoney");

		$this->plugin = $plugin;
	}

	public function execute(CommandSender $sender, string $label, array $params) : bool{
		if(!$sender->hasPermission($this->getPermission())){
			$sender->sendMessage(EconomyAPI::$prefix . "이 명령을 사용할 권한이 없습니다.");
			return true;
		}

		$page = (int)array_shift($params);

		$data = $this->plugin->getAllMoney();

		ServerUtils::sendRanking($sender, $data, "돈", $page, 5, "", function($value){
            return $this->plugin->koreanWonFormat($value);
        });

		return true;
	}
}
