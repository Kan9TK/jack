<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\economy;

use pocketmine\Server;

class EconomyProviderManager{

    protected array $providers = [];

    public function __construct(){
        $economyProviders = [
            EconomySProvider::getName() => new EconomySProvider,
            CashProvider::getName() => new CashProvider
        ];
        foreach ( $economyProviders as $name => $provider ){
            if(Server::getInstance()->getPluginManager()->getPlugin($name)!==null){
                $this->providers[$name] = $provider;
            }
        }
    }

    public function getProvider(string $name) : ?EconomyProvider{
        return $this->providers[$name] ?? null;
    }

    public function getProviders() : array{
        return $this->providers;
    }

}