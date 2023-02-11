<?php


namespace kang\fishing\item;

use kang\fishing\Fishing;
use kang\fishing\projectile\FishingHook;
use pocketmine\entity\Location;

use pocketmine\item\ItemUseResult;
use pocketmine\item\Tool;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\player\Player;
use pocketmine\math\Vector3;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;

class FishingRod extends Tool
{

	protected $cool = [], $click = [];
	public static $list = [];
	
    public function getMaxDurability () : int{
        return 50;
    }

    public function getMaxStackSize () : int{
        return 1;
    }

    public function onClickAir(Player $player, Vector3 $directionVector): ItemUseResult{

        $sessionManager = Fishing::getInstance()->getSessionManager();
        if ($sessionManager->getFishingHook($player) == null) { // 낚시 중이 아님.

            $location = $player->getLocation();

            /*$nbt = CompoundTag::create()
                ->setTag('Pos', new ListTag([
                    new DoubleTag ($player->getTargetBlock(2)->getPosition()->x),
                    new DoubleTag ($player->getPosition()->y + $player->getEyeHeight()),
                    new DoubleTag ($player->getTargetBlock(2)->getPosition()->z)
                ]))
                ->setTag('Motion', new ListTag([
                    new DoubleTag (-sin($location->yaw / 180 * M_PI) * cos($location->pitch / 180 * M_PI)),
                    new DoubleTag (-sin($location->pitch / 180 * M_PI)),
                    new DoubleTag (cos($location->yaw / 180 * M_PI) * cos($location->pitch / 180 * M_PI))
                ]))
                ->setTag('Rotation', new ListTag([
                    new FloatTag (($location->yaw > 180 ? 360 : 0) - $location->yaw),
                    new FloatTag (-$location->pitch)
                ]));*/

            //$fishingHook = new FishingHook($location, $player, $nbt);

            $fishingHook = new FishingHook(Location::fromObject(
                $player->getEyePos(),
                $location->getWorld(),
                ($location->yaw > 180 ? 360 : 0) - $location->yaw,
                -$location->pitch
            ), $player);

            $fishingHook->setMotion($player->getDirectionVector());
            $fishingHook->setOwningEntity($player);

            $fishingHook->spawnToAll();

            $fishingHook->setNameTag("{$player->getName()}님의 낚시찌");
            $fishingHook->setNameTagVisible(true);
            $fishingHook->setNameTagAlwaysVisible(false);

            $sessionManager->startFishing($player, $fishingHook);

            $location->getWorld()->broadcastPacketToViewers($location, LevelSoundEventPacket::nonActorSound(LevelSoundEvent::BOW, $location->asVector3(), false));

            $player->sendActionBarMessage("잠수 낚시를 시작해요. (직접 낚을 수 없어요.)");

        } else { // 낚시 중

            $fishingHook = $sessionManager->getFishingHook($player);
            if ( $fishingHook !== null ){
                $fishingHook->close();
            }
            $sessionManager->finishFishing($player);

            $player->sendActionBarMessage("잠수 낚시를 종료했어요.");

        }

        return parent::onClickAir($player, $directionVector);
    }

}
