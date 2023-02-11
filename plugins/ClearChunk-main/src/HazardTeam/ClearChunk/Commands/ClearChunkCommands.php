<?php

declare(strict_types=1);

namespace HazardTeam\ClearChunk\Commands;

use HazardTeam\ClearChunk\ClearChunk;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat;
use function implode;

final class ClearChunkCommands extends Command implements PluginOwned
{

	public function __construct(protected ClearChunk $plugin)
	{
		parent::__construct('청크청소', '청크를 청소해요.');
		$this->setPermission('autoclearchunk.clearchunk');
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args): bool
	{
		if (!$this->testPermission($sender)) {
			return false;
		}

		// if from console they have to enter world name, and if player name world is optional
		$world = null;
		if (!$sender instanceof Player) {
			if (!isset($args[0])) {
				$sender->sendMessage(TextFormat::RED . 'Please input world name');
				return false;
			}
			$world = implode(' ', $args);
			$world = $this->plugin->getServer()->getWorldManager()->getWorldByName($world);
			if ( $world == null ){
                $sender->sendMessage(TextFormat::RED . 'Cant find world');
			    return false;
            }
		} else {
			// Check if $world variable null
			if ($world === null) {
				// for add custom world optional
				if (isset($args[0])) {
					$world = implode(' ', $args);
				} else {
					// if args 0 null this will get player world
					$world = $sender->getWorld();
				}
			}
		}
		// now clear the world chunk
		$this->getOwningPlugin()->clearChunk($world, $sender);
		return true;
	}

	public function getOwningPlugin(): ClearChunk
	{
		return $this->plugin;
	}
}
