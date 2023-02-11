<?php

declare(strict_types=1);

namespace alvin0319\Area\area;

class AreaProperties{
	public const PVP = "전투";
	public const PROTECT = "보호";
	public const INVENTORY_SAVE = "인벤토리세이브";

	public const DEFAULTS = [
		self::PVP => true,
		self::PROTECT => true,
		self::INVENTORY_SAVE => true
	];
	/** @var Area */
	protected Area $area;
	/** @var array */
	protected array $data = self::DEFAULTS;

	public function __construct(array $data = self::DEFAULTS){
		$this->data = $data;

		$this->fix();
	}

	private function fix() : void{
		foreach(self::DEFAULTS as $key => $value){
			if(!isset($this->data[$key])){
				$this->data[$key] = $value;
			}
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