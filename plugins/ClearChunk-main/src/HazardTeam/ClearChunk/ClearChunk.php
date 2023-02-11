<?php

declare(strict_types=1);

namespace HazardTeam\ClearChunk;

use HazardTeam\ClearChunk\Commands\ClearAllChunkCommands;
use HazardTeam\ClearChunk\Commands\ClearChunkCommands;
use kang\ServerUtils\ServerUtils;
use leinne\pureentities\entity\LivingBase;
use pocketmine\command\CommandSender;
use pocketmine\entity\object\ItemEntity;
use pocketmine\event\Listener;
use pocketmine\event\world\WorldLoadEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\TextFormat;
use pocketmine\world\World;
use function array_diff;
use function count;
use function gettype;
use function in_array;
use function is_string;
use function scandir;
use function str_replace;

final class ClearChunk extends PluginBase implements Listener
{
	public const WORLDS = [
	    "wild",
	    "town"
    ];

	public function onEnable(): void{

		$this->getScheduler()->scheduleDelayedRepeatingTask(new ClosureTask(
			function (): void {
				$this->clearAllChunk();
			}
		), 20 * 600, 20 * 600);

		$this->getServer()->getCommandMap()->register('ClearChunk', new ClearChunkCommands($this));
		$this->getServer()->getCommandMap()->register('ClearChunk', new ClearAllChunkCommands($this));
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function clearAllChunk(?CommandSender $sender = null): void
	{
		$cleared = 0;

		foreach (self::WORLDS as $worldName) {
			$world = $this->getServer()->getWorldManager()->getWorldByName($worldName);
			if ($world !== null) {
				$this->clearChunk($world);
			}
		}

		if ($sender === null) {
			//$this->getServer()->getLogger()->info($cleared." chunks cleared");
		} else {
			ServerUtils::msg($sender, $cleared." 청크를 청소했어요.");
		}
	}

	public function clearChunk(World $world, ?CommandSender $sender = null) : void{
		$cleared = 0;
		foreach ($world->getLoadedChunks() as $chunkHash => $chunk) {
			World::getXZ($chunkHash, $chunkX, $chunkZ); // For getting chunk X and Z
			$count = count($world->getChunkPlayers($chunkX, $chunkZ)); //Check if the player is in the chunk
			if ($count === 0) {
				$cleared++;
				$world->unloadChunk($chunkX, $chunkZ); // Unload Chunk
			}
		}
        if ($sender === null) {
            //$this->getServer()->getLogger()->info($cleared." chunks cleared");
        } else {
            ServerUtils::msg($sender, $cleared." 청크를 청소했어요.");
        }
	}

}
