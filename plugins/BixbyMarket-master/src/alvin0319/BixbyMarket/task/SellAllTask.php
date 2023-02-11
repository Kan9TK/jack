<?php

namespace alvin0319\BixbyMarket\task;

use alvin0319\BixbyMarket\BixbyMarket;
use alvin0319\BixbyMarket\category\Category;
use alvin0319\BixbyMarket\economy\EconomyProvider;
use kang\ServerUtils\ServerUtils;
use onebone\economyapi\EconomyAPI;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use pocketmine\item\Item;
use pocketmine\scheduler\Task;

class SellAllTask extends Task {

    private BixbyMarket $plugin;

    //private static array $tasks = [];

    private int $inventoryIndex = 0;

    private int $totalEarned = 0;

    private array  $totalSelled = [];

    public function __construct(private Category $category, private Player $player, private EconomyProvider $economyProvider) {
        $this->plugin = BixbyMarket::getInstance();
    }

    public function start() {
        /*$name = $this->player->getName();
        if (isset(self::$tasks[$name])) {
            throw new SellException("이미 아이템을 판매중입니다.");
        }
        self::$tasks[$name] = $this;*/
        BixbyMarket::getInstance()->getScheduler()->scheduleRepeatingTask($this, 4);
    }

    public function onRun() : void{
        if (!$this->player->isOnline()) {
            $this->close();
            return;
        }

        while ($this->inventoryIndex < $this->player->getInventory()->getSize()) {
            $content = $this->player->getInventory()->getItem($this->inventoryIndex++);
            if (!$content instanceof Item || $content->getId() == ItemIds::AIR) {
                continue;
            }
            $market = BixbyMarket::getInstance()->getMarketManager()->getMarketByItem($content);
            if ($market === null || $market->getSellPrice() <= 0) {
                continue;
            }

            $money_before = $this->economyProvider->getMoney($this->player);

            $market->sell($this->player, $content->getCount(), $this->economyProvider);

            $money_after = $this->economyProvider->getMoney($this->player);

            $earned = $money_after - $money_before;
            $this->totalEarned += $earned;

            $itemname = $content->getName();
            $this->totalSelled[$itemname] = ($this->totalSelled[$itemname] ?? 0) + $content->getCount();

            continue;
        }
        // end
        ServerUtils::msg($this->player, "판매된 아이템 : " . implode(", ", array_map(function ($key, $value) {
                return $key . " " . $value . "개";
            }, array_keys($this->totalSelled), $this->totalSelled)));
        ServerUtils::msg($this->player, "판매 합계 : " . EconomyAPI::getInstance()->koreanWonFormat($this->totalEarned).$this->economyProvider->getUnit());
        $this->close();
    }

    public function close() {
        //unset(self::$tasks[$this->player->getName()]);
        //unset($this->player);
        $this->getHandler()->cancel();
    }
}