<?php

declare(strict_types=1);

namespace alvin0319\Area\world;

class WorldData{

	public const PROTECT = "보호";
	public const PVP = "전투";
    public const INVENTORY_SAVE = "인벤토리세이브";
    public const ITEM_DROP = "아이템드랍";

	public const DEFAULTS = [
		self::PROTECT => true,
		self::PVP => false,
        self::INVENTORY_SAVE => true,
        self::ITEM_DROP => true
	];

	protected array $data = self::DEFAULTS;

	public function __construct(array $data = self::DEFAULTS){
		$this->data = $data;
		$this->fix();
	}

	private function fix() : void{
		foreach(self::DEFAULTS as $name => $value){
			if(!isset($this->data[$name]))
				$this->data[$name] = $value;
		}
	}

	public function get(string $option){
		return $this->data[$option] ?? null;
	}

	public function set(string $option, $value){
		$this->data[$option] = $value;
	}

	public function jsonSerialize() : array{
		return $this->data;
	}

}