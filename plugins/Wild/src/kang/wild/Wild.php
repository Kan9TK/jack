<?php

declare(strict_types=1);

namespace kang\wild;

use czechpmdevs\multiworld\util\WorldUtils;
use kang\dataconfig\DataConfig;
use kang\ServerUtils\ServerUtils;
use kang\wild\command\NormalWildCommand;
use kang\wild\command\ResetWildCommand;
use kang\wild\command\TownWildCommand;
use kang\wild\command\WildCommand;
use kang\wild\listener\EventListener;
use kang\wild\task\DeleteWorldTask;
use kang\wild\task\ExtractWorldTask;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\world\Position;
use pocketmine\world\WorldCreationOptions;
use skymin\asyncqueue\AsyncQueue;

class Wild extends PluginBase
{

    private static Wild $instance;

    protected DataConfig $config;

    public const WILD_WORLD = "wild";
    public const TOWN_WORLD = "town";

    public const minX = -10000;
    public const maxX = 10000;

    public const minZ = -10000;
    public const maxZ = 10000;

    public const RESET_CYCLE_DAY = 7;


    public static function getInstance(): Wild
    {
        return self::$instance;
    }

    public function onLoad(): void
    {
        self::$instance = $this;
    }

    public function onEnable(): void
    {
        $this->config = new DataConfig($this->getDataFolder()."config.json", DataConfig::TYPE_JSON, ["last"=>time()]);

        $now = new \DateTime('now');
        $last = (new \DateTime())->setTimestamp($this->config->get("last"));
        $diff = date_diff($now, $last);

        if ( $diff->days >= self::RESET_CYCLE_DAY ){
            $this->resetWild(self::WILD_WORLD);
            $this->config->set("last", time());
        }

        $this->getServer()->getCommandMap()->registerAll(strtolower($this->getName()), [
            new WildCommand($this),
            new NormalWildCommand($this),
            new TownWildCommand($this),
            new ResetWildCommand($this)
        ]);
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    }

    public function onDisable(): void{
        $this->config->save($this->config->data);
    }

    public function RandomTeleport(Player $player, string $worldName, string $wildType): void
    {
        $worldManager = $this->getServer()->getWorldManager();
        if (($world = $worldManager->getWorldByName($worldName)) == null) {
            ServerUtils::error($player, "앗! 이동에 문제가 생겼어요.");
            return;
        }

        $x = mt_rand(self::minX, self::maxX);
        $z = mt_rand(self::minZ, self::maxZ);

        $x = (int)floor($x);
        $z = (int)floor($z);

        $player->sendTitle("§l이동 중..", "잠시만 기다려주세요!");

        $world->orderChunkPopulation($x >> 4, $z >> 4, null)->onCompletion(function () use ($player, $x, $z, $world, $wildType): void {
            if ($player !== null) {
                $pos = new Vector3($x, $world->getHighestBlockAt($x, $z) + 1.0, $z);
                if ($player->teleport(Position::fromObject($pos, $world))) {
                    $player->sendTitle("§l" . $wildType, "청크 로딩에 시간이 걸릴 수 있어요!");
                }
            }
        }, function () use ($player): void {
            if ($player !== null) {
                ServerUtils::error($player, "앗! 이동에 문제가 생겼어요.");
            }
        });
    }

    public function resetWild(string $worldName, bool $broad = false): void{
        $worldManager = $this->getServer()->getWorldManager();
        if (($world = $worldManager->getWorldByName($worldName)) == null) {
            return;
        }
        foreach ($world->getPlayers() as $player) {
            ServerUtils::error($player, "야생 초기화로 인해 안전하게 스폰으로 이동되었어요!");
            $player->teleport($worldManager->getDefaultWorld()->getSafeSpawn());
        }

        if ( $broad ){
            ServerUtils::broad("야생 초기화가 시작되었어요. 잠시 렉이 걸릴 수 있어요.");
        }

        WorldUtils::removeWorld($worldName);

        $generator = WorldUtils::getGeneratorByName("vanilla_overworld");
        Wild::getInstance()->getServer()->getWorldManager()->generateWorld(
            name: $worldName,
            options: WorldCreationOptions::create()
            ->setSeed(0)
            ->setGeneratorClass($generator->getGeneratorClass())
        );

        if ( $broad ){
            ServerUtils::broad("성공적으로 야생이 초기화되었어요. 30초 후에 입장하면 더욱 안전해요.");
        }

    }

}