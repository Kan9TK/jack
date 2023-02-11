<?php

declare(strict_types=1);

namespace leinne\pureentities\item;

use pocketmine\block\Block;
use pocketmine\block\BlockLegacyIds;
use pocketmine\entity\Location;
use pocketmine\item\Boat as PMBoat;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\player\Player;

class Boat extends PMBoat{

    public function onInteractBlock(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector): ItemUseResult
    {
        if ( $blockClicked->getPosition()->getWorld()->getBlock($blockClicked->getPosition()->add(0,1,0))->getId() == BlockLegacyIds::WATER ){
            return ItemUseResult::SUCCESS();
        }
        $location = Location::fromObject($blockClicked->getPosition()->getSide($face)->add(0.5, 0, 0.5), $blockClicked->getPosition()->getWorld());
        $boat = new \leinne\pureentities\entity\vehicle\Boat($location);
        $boat->getNetworkProperties()->setInt(EntityMetadataProperties::VARIANT, $this->getMeta());
        $boat->spawnToAll();
        return ItemUseResult::SUCCESS();
    }

}