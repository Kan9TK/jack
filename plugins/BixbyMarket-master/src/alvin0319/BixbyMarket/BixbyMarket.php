<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket;

use alvin0319\BixbyMarket\category\CategoryManager;
use alvin0319\BixbyMarket\command\ManageMarketCommand;
use alvin0319\BixbyMarket\command\MarketCommand;
use alvin0319\BixbyMarket\economy\EconomyProviderManager;
use alvin0319\BixbyMarket\entity\EntityManager;
use alvin0319\BixbyMarket\market\MarketManager;
use alvin0319\BixbyMarket\shop\ShopManager;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use function array_filter;
use function pathinfo;
use function scandir;

final class BixbyMarket extends PluginBase{
	use SingletonTrait;

	public static string $prefix = "§b§l[Market] §r§7";

	protected MarketManager $marketManager;

	protected CategoryManager $categoryManager;

	protected ShopManager $shopManager;

	protected EntityManager $entityManager;

	protected EconomyProviderManager $economyProviderManager;

	public static function getInstance(): BixbyMarket{
	    return self::$instance;
    }

    public function onLoad() : void{
		self::setInstance($this);
	}

	public function onEnable() : void{
		$this->saveDefaultConfig();
		if(!InvMenuHandler::isRegistered()){
			InvMenuHandler::register($this);
		}
        $this->categoryManager = new CategoryManager();
		$this->marketManager = new MarketManager();
        $this->economyProviderManager = new EconomyProviderManager();
		$this->shopManager = new ShopManager();
		$this->entityManager = new EntityManager();

		$this->getServer()->getCommandMap()->registerAll("bixbymarket", [
            new ManageMarketCommand($this)
		]);
	}

	public function onDisable() : void{
		$this->marketManager->save();
		$this->shopManager->save();
	}

	public function getMarketManager() : MarketManager{
		return $this->marketManager;
	}

	public function getCategoryManager() : CategoryManager{
		return $this->categoryManager;
	}

	public function getShopManager() : ShopManager{
	    return $this->shopManager;
    }

    public function getEntityManager() : EntityManager{
	    return $this->entityManager;
    }

    public function getEconomyProviderManager() : EconomyProviderManager{
	    return $this->economyProviderManager;
    }

}