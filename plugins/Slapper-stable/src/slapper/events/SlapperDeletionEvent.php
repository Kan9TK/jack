<?php

declare(strict_types=1);

namespace slapper\events;

use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityEvent;
use slapper\SlapperInterface;

/** @phpstan-extends EntityEvent<Entity&SlapperInterface> */
class SlapperDeletionEvent extends EntityEvent {

    /** @param Entity&SlapperInterface $entity */
    public function __construct(Entity $entity) {
        $this->entity = $entity;
    }

}
