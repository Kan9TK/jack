<?php

declare(strict_types=1);

namespace slapper\entities\other;

use pocketmine\data\bedrock\EntityLegacyIds;

use slapper\entities\SlapperEntity;

class SlapperBoat extends SlapperEntity {

    const TYPE_ID = EntityLegacyIds::BOAT;
    const HEIGHT = 0.6;

}
