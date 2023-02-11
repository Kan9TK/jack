<?php

declare(strict_types=1);

namespace Aericio\PCEAllyChecks;

use Aericio\PCEAllyChecks\tasks\CheckUpdatesTask;
use alvin0319\Area\AreaLoader;
use alvin0319\Area\world\WorldData;
use DaPigGuy\PiggyCustomEnchants\utils\AllyChecks;
use kang\towny\Towny;
use pocketmine\entity\Entity;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginManager;

class PCEAllyChecks extends PluginBase
{

    protected PluginManager $pluginManager;

    public function onEnable(): void
    {
        $this->pluginManager = $this->getServer()->getPluginManager();

        if (!is_null( $this->pluginManager->getPlugin("Area") )) {
            AllyChecks::addCheck($this, function (Player $player, Entity $entity): bool {
                $pl = AreaLoader::getInstance();
                if ( $pl->getWorldManager()->get($player->getWorld())->get(WorldData::PROTECT) ){
                    return true;
                }
                return false;
            });
        }

        if (!is_null( $this->pluginManager->getPlugin("Towny") )) {
            AllyChecks::addCheck($this, function (Player $player, Entity $entity): bool {
                $pl = Towny::getInstance();
                if ( $entity instanceof Player ){
                    if ( $pl->getTownManager()->getTownByName($player->getName()) === $pl->getTownManager()->getTownByName($entity->getName()) ){
                        return true;
                    }
                }
                return false;
            });
        }

    }
}