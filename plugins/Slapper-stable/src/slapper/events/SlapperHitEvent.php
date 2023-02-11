<?php

declare(strict_types=1);

namespace slapper\events;

use pocketmine\entity\Entity;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\entity\EntityEvent;
use pocketmine\player\Player;
use slapper\SlapperInterface;

/** @phpstan-extends EntityEvent<Entity&SlapperInterface> */
class SlapperHitEvent extends EntityEvent implements Cancellable {
    use CancellableTrait;

    /** @var Player */
    private $damager;

    /** @param Entity&SlapperInterface $entity */
    public function __construct(Entity $entity, Player $damager) {
        $this->entity = $entity;
        $this->damager = $damager;
    }

    public function getDamager(): Player {
        return $this->damager;
    }
}
