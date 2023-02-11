<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyCustomEnchants\enchants\weapons;

use DaPigGuy\PiggyCustomEnchants\enchants\ReactiveEnchantment;
use DaPigGuy\PiggyCustomEnchants\entities\PiggyLightning;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Event;
use pocketmine\inventory\Inventory;
use DaPigGuy\PiggyCustomEnchants\enchants\Rarity;
use pocketmine\item\Item;
use pocketmine\player\Player;

class LightningEnchant extends ReactiveEnchantment
{
    public string $name = "lightning";
    public int $rarity = Rarity::LEGENDARY;

    public function react(Player $player, Item $item, Inventory $inventory, int $slot, Event $event, int $level, int $stack): void
    {
        if ($event instanceof EntityDamageByEntityEvent) {
            $lightning = new PiggyLightning($event->getEntity()->getLocation());
            $lightning->setOwningEntity($player);
            $lightning->spawnToAll();
        }
    }
}