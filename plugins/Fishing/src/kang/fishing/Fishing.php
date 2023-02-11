<?php


namespace kang\fishing;

use kang\dataconfig\DataConfig;
use kang\fishing\command\FishingCommand;
use kang\fishing\command\ManageFishingCommand;
use kang\fishing\item\FishingRod;
use kang\fishing\projectile\FishingHook;
use kang\fishing\session\SessionManager;
use kang\fishing\fish\FishManager;

use muqsit\invmenu\InvMenuHandler;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\plugin\PluginBase;
use pocketmine\item\ItemFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;


class Fishing extends PluginBase
{

    protected static Fishing $instance;

    public DataConfig $config;

    protected SessionManager $sessionManager;
    protected FishManager $fishManager;
    protected FishingContestManager $contestManager;

    public static function getInstance() : Fishing{
        return self::$instance;
    }

    public function onLoad() : void{
        self::$instance = $this;
    }

    public function onEnable(): void{

        $this->sessionManager = new SessionManager($this);
        $this->fishManager = new FishManager($this);
        $this->contestManager = new FishingContestManager($this);

        $this->getServer()->getCommandMap()->registerAll($this->getName(), [
            new FishingCommand($this, "낚시", "낚시 명령어예요."),
            new ManageFishingCommand($this, "낚시관리", "낚시 관리 명령어예요.")
        ]);

        if ( $this->contestManager->isStart() ){
            $this->getScheduler()->scheduleRepeatingTask($this->contestManager->getTask(), 20*60);
        }

        EntityFactory::getInstance()->register(FishingHook::class, function(World $world, CompoundTag $nbt) : FishingHook{
            return new FishingHook(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
        }, ["FishingHook", "minecraft:fishing_hook"], EntityLegacyIds::FISHING_HOOK);
        ItemFactory::getInstance()->register(new FishingRod(new ItemIdentifier(ItemIds::FISHING_ROD, 0)), true);
        if(!InvMenuHandler::isRegistered())InvMenuHandler::register($this);
    }

    public function onDisable(): void
    {
        foreach ($this->getServer()->getWorldManager()->getWorlds() as $level) {
            foreach ($level->getEntities() as $entity) {
                if ($entity instanceof FishingHook) {
                    $entity->close();
                }
            }
        }
        $this->fishManager->save();
        $this->contestManager->save();
    }

    public function getSessionManager() : SessionManager{
        return $this->sessionManager;
    }

    public function getFishManager() : FishManager{
        return $this->fishManager;
    }

    public function getContestManager() : FishingContestManager{
        return $this->contestManager;
    }


}