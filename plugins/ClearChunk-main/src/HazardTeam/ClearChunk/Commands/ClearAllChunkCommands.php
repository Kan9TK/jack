<?php

declare(strict_types=1);

namespace HazardTeam\ClearChunk\Commands;

use HazardTeam\ClearChunk\ClearChunk;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginOwned;

final class ClearAllChunkCommands extends Command implements PluginOwned
{

	public function __construct(protected ClearChunk $plugin)
	{
		parent::__construct('청크전체청소', '전체 청크를 청소해요.');
		$this->setPermission('autoclearchunk.clearchunkall');
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args): bool
	{
		if (!$this->testPermission($sender)) {
			return false;
		}

		// now clear the world chunk
		// message clear chunk only goes to CommandSender
		$this->getOwningPlugin()->clearAllChunk($sender);
		return true;
	}

	public function getOwningPlugin(): ClearChunk
	{
		return $this->plugin;
	}
}
