<?php


namespace kang\fishing\projectile;

use kang\fishing\Fishing;
use kang\ServerUtils\ServerUtils;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\entity\projectile\Projectile;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class FishingHook extends Projectile
{

    protected $gravity = 0.1;

    protected int $fishTick;
    protected int $groundTick;
    protected bool $motionChanged = false;

    public function __construct(Location $location, ?Entity $shootingEntity, ?CompoundTag $nbt = null)
    {
        parent::__construct($location, $shootingEntity, $nbt);
        $this->fishTick = mt_rand(20*5, 20*30);
        $this->groundTick = 20*2;

    }

    public function onUpdate (int $currentTick): bool
    {

        if ($this->isFlaggedForDespawn() or $this->isClosed()) {
            return false;
        }

        $player = $this->getOwningEntity();

        if (!$player instanceof Player) {
            if (!$this->isFlaggedForDespawn()) {
                $this->flagForDespawn();
            }
            return false;
        }

        if ($player->getInventory()->getItemInHand()->getId() !== ItemIds::FISHING_ROD) {
            $player->sendActionBarMessage("잠수 낚시가 종료되었어요.");
            $this->flagForDespawn();
            return false;
        }

        if ($this->getPosition()->distance($player->getPosition()) > 33) {
            $player->sendActionBarMessage("낚시찌와 너무 멀어요. (최대 33 블럭)");
            $this->flagForDespawn();
            return false;
        }

        if ($this->fishTick-- > 0) {

            if (!$this->isUnderwater() && $this->groundTick-- < 0) {
                $player->sendActionBarMessage("낚시찌를 물에 던져보세요!");
                $this->flagForDespawn();
                return false;
            }
            if ($this->isUnderwater()) {
                if ( $this->gravityEnabled ) {
                    $this->motion->y = 0.1;
                    $this->motion->x = 0;
                    $this->motion->z = 0;
                }
                $this->motion->y += 0.0001;
            }

        } else {

            $this->motion->y -= 0.008;
            $this->fishTick = mt_rand(20 * 5, 20 * 30);
            $this->groundTick = 20 * 2;

            $player->getXpManager()->addXp(mt_rand(1, 6), true);

            $fishManager = Fishing::getInstance()->getFishManager();
            $contestManager = Fishing::getInstance()->getContestManager();

            $fish = $fishManager->createFish($player);
            ServerUtils::msg($player, $fish->getCustomName() . " §r§7(을)를 낚았어요! (보관함으로 이동해요.)");
            $fishManager->addFish($player, $fish);

            if ( $contestManager->isStart() ) {
                if ($contestManager->checkRanking($player, $fish)) {
                    ServerUtils::msg($player, "낚시 대회 기록을 갱신했어요! (/낚시 대회)");
                }
                $contestManager->sendRanking($player);
            }

        }

        return parent::onUpdate($currentTick);

    }

    protected function getInitialSizeInfo() : EntitySizeInfo{
        return new EntitySizeInfo(0.25, 0.25);
    }

    public static function getNetworkTypeId() : string{
        return EntityIds::FISHING_HOOK;
    }

}