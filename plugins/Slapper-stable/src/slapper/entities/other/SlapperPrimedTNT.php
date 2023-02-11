<?php

declare(strict_types=1);

namespace slapper\entities\other;

use pocketmine\data\bedrock\EntityLegacyIds;

use slapper\entities\SlapperEntity;

class SlapperPrimedTNT extends SlapperEntity {

    const TYPE_ID = EntityLegacyIds::TNT;
    const HEIGHT = 0.98;

}