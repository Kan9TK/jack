<?php

declare(strict_types=1);

namespace slapper\entities\other;

use pocketmine\data\bedrock\EntityLegacyIds;

use slapper\entities\SlapperEntity;

class SlapperMinecart extends SlapperEntity {

    const TYPE_ID = EntityLegacyIds::MINECART;
    const HEIGHT = 0.7;

}
