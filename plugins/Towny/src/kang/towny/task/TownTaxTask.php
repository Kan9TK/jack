<?php

declare(strict_types=1);

namespace kang\towny\task;

use kang\ServerUtils\ServerUtils;
use kang\towny\town\Town;
use kang\towny\town\TownFactory;
use kang\towny\town\TownManager;
use kang\towny\Towny;
use onebone\economyapi\EconomyAPI;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class TownTaxTask extends Task{

    protected TownManager $townManager;
    protected EconomyAPI $economyAPI;
    protected Server $server;

    public function __construct(private Towny $plugin){
        $this->townManager = $plugin->getTownManager();
        $this->economyAPI = EconomyAPI::getInstance();
        $this->server = Server::getInstance();
    }

    public function onRun(): void
    {
        if ( $this->townManager->getTaxLastDay() == date("d") ) return;

        if ( date("G") < 12 ) return;

        foreach ( $this->townManager->getAllTowns() as $ownerName => $town ){

            if ( $town instanceof Town ){

                if ( $town->getMoney() < $town->getMaintenance() ){
                    //$town->broadMsg("마을 유지비를 내지 못하여 마을이 해체되었어요.");
                    TownFactory::deleteTown($ownerName);
                    ServerUtils::broad($town->getName()." 마을이 유지비를 내지 못하여 해체되었어요.");
                    continue;
                }

                $town->reduceMoney($town->getMaintenance());
                $town->broadMsg("마을 유지비 ".$town->getMaintenance()."원이 출금되었어요.");

                foreach ( array_keys($town->getMembers()) as $memberName ){

                    if ( $town->isOwner($memberName) ) continue;

                    if ( $this->economyAPI->myMoney($memberName) < $town->getTax() ){
                        $town->kick($memberName);
                        if ( ($member = $this->server->getPlayerExact($memberName)) !== null ){
                            ServerUtils::error($member, "마을의 세금을 내지 못하여 마을에서 추방되었어요.");
                        }
                        $town->broadMsg($memberName."님이 세금을 내지 못하여 마을에서 추방되었어요.");
                        continue;
                    }

                    $this->economyAPI->reduceMoney($memberName, $town->getTax());
                    if ( ($member = $this->server->getPlayerExact($memberName)) !== null ){
                        ServerUtils::msg($member, "마을의 세금 ".$town->getTax()."원을 납부했어요.");
                    }

                }

            }
        }

        $this->townManager->updateTaxLastDay();

    }

}