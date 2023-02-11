<?php

declare(strict_types=1);

namespace alvin0319\Area\area;

use pocketmine\data\bedrock\BiomeIds;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\format\BiomeArray;
use pocketmine\world\format\Chunk;
use pocketmine\world\Position;
use function array_search;
use function array_values;
use function in_array;
use function max;
use function min;
use function strtolower;

class Area{
	/** @var int */
	protected int $id;
	/** @var int */
	protected int $x1;
	/** @var int */
	protected int $x2;
	/** @var int */
	protected int $z1;
	/** @var int */
	protected int $z2;
	/** @var string */
	protected string $world;
	/** @var AreaProperties */
	protected AreaProperties $areaProperties;

	public function __construct(int $id, int $x1, int $x2, int $z1, int $z2, string $world, ?AreaProperties $properties = null){
		$this->id = $id;
		$this->x1 = $x1;
		$this->x2 = $x2;
		$this->z1 = $z1;
		$this->z2 = $z2;
		$this->world = $world;
		$this->areaProperties = $properties ?? new AreaProperties();
	}

	public function getId() : int{
		return $this->id;
	}

	public function getWorld() : string{
		return $this->world;
	}

	public function getMinX() : int{
		return min($this->x1, $this->x2);
	}

	public function getMaxX() : int{
		return max($this->x1, $this->x2);
	}

	public function getMinZ() : int{
		return min($this->z1, $this->z2);
	}

	public function getMaxZ() : int{
		return max($this->z1, $this->z2);
	}

	public function getAreaProperties() : AreaProperties{
		return $this->areaProperties;
	}

	public function getCenter() : Vector3{
		$xSize = $this->getMaxX() - $this->getMinX();
		$zSize = $this->getMaxZ() - $this->getMinZ();
		$x = $this->getMinX() + ($xSize / 2);
		$z = $this->getMinZ() + ($zSize / 2);
		$server = Server::getInstance();
		$world = $server->getWorldManager()->getWorldByName($this->world);
		$chunk = $world->loadChunk($x >> 4, $z >> 4);
		if($chunk === null){
			$chunk = new Chunk([], BiomeArray::fill(BiomeIds::PLAINS), false);
			$world->setChunk($x >> 4, $z >> 4, $chunk);
		}
		$y = $world->getHighestBlockAt((int) $x, (int) $z);
		return new Vector3($x, $y + 1, $z);
	}

	public function moveTo(Player $player) : void{
		$pos = Position::fromObject($this->getCenter(), $player->getServer()->getWorldManager()->getWorldByName($this->world));
		$player->teleport($pos);
	}

	public function jsonSerialize() : array{
		return [
			"id" => $this->id,
			"x1" => $this->x1,
			"x2" => $this->x2,
			"z1" => $this->z1,
			"z2" => $this->z2,
			"world" => $this->world,
			"properties" => $this->areaProperties->jsonSerialize()
		];
	}

	public static function jsonDeserialize(array $data) : Area{
		return new Area($data["id"], $data["x1"], $data["x2"], $data["z1"], $data["z2"], $data["world"], new AreaProperties($data["properties"]));
	}

	public function equals(Area $that) : bool{
		return $this->getCenter()->equals($that->getCenter()) && $this->getId() === $that->getId() && $this->getWorld() === $that->getWorld();
	}
}