<?php

declare(strict_types=1);

namespace TeamBixby\CustomCraft;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\InvMenuHandler;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\crafting\FurnaceRecipe;
use pocketmine\crafting\FurnaceType;
use pocketmine\crafting\ShapedRecipe;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use TeamBixby\CustomCraft\command\ManageCraftCommand;

use function array_map;
use function array_merge;
use function array_values;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function in_array;
use function json_decode;
use function json_encode;

class CustomCraft extends PluginBase{
	use SingletonTrait;

	protected $data = [
		"crafting" => [],
		"furnace" => []
	];

	public static function getInstance() : CustomCraft{
	    return self::$instance;
    }

	public function onLoad() : void{
		self::setInstance($this);
	}

	public function onEnable() : void{
		if(file_exists($file = $this->getDataFolder() . "custom_craft_data.json")){
			$this->data = json_decode(file_get_contents($file), true);
		}
		$this->registerShapedRecipes();
		$this->registerFurnaceRecipes();

		//$this->getServer()->getCraftingManager()->buildCraftingDataCache();

		$this->getServer()->getCommandMap()->register("mcc", new ManageCraftCommand($this, "mcc", "mcc command"));

		if(!InvMenuHandler::isRegistered()){
			InvMenuHandler::register($this);
		}
	}

	public function onDisable() : void{
		file_put_contents($this->getDataFolder() . "custom_craft_data.json", json_encode($this->data));
	}

	public function registerShapedRecipes() : void{
		$manager = $this->getServer()->getCraftingManager();
		foreach($this->data["crafting"] as $craftData){
			[$a, $b, $c, $d, $e, $f, $g, $h, $i] = array_map(function(array $data) : Item{
				return Item::jsonDeserialize($data);
			}, $craftData["input"]);
			$output = Item::jsonDeserialize($craftData["output"]);
			$recipe = new ShapedRecipe([
				"ABC",
				"DEF",
				"GHI"
			], [
				"A" => $a,
				"B" => $b,
				"C" => $c,
				"D" => $d,
				"E" => $e,
				"F" => $f,
				"G" => $g,
				"H" => $h,
				"I" => $i
			], [$output]);
			$manager->registerShapedRecipe($recipe);
		}
	}

	public function registerFurnaceRecipes() : void{
		$manager = $this->getServer()->getCraftingManager();
		foreach($this->data["furnace"] as $furnaceData){
			$input = Item::jsonDeserialize($furnaceData["input"]);
			$output = Item::jsonDeserialize($furnaceData["output"]);
			$recipe = new FurnaceRecipe($input, $output);
			$manager->getFurnaceRecipeManager(FurnaceType::FURNACE())->register($recipe);
		}
	}

	public function registerShapedRecipe(array $items, Item $output) : void{
		$recipe = new ShapedRecipe(["ABC", "DEF", "GHI"], $items, [$output]);
		$this->getServer()->getCraftingManager()->registerShapedRecipe($recipe);
		//$this->syncCraftingData();

		$this->data["crafting"][] = [
			"input" => array_map(function(Item $item) : array{
				return $item->jsonSerialize();
			}, array_values($items)),
			"output" => $output->jsonSerialize()
		];
	}

	public function registerFurnaceRecipe(Item $input, Item $output) : void{
		$recipe = new FurnaceRecipe($output, $input);
		$this->getServer()->getCraftingManager()->getFurnaceRecipeManager(FurnaceType::FURNACE())->register($recipe);
		$this->syncCraftingData();

		$this->data["furnace"][] = ["input" => $input->jsonSerialize(), "output" => $output->jsonSerialize()];
	}

	public function getAllShapedRecipe() : array{
	    return $this->data["crafting"];
    }

    public function getAllFurnaceRecipe() : array{
        return $this->data["furnace"];
    }

	private function syncCraftingData() : void{
		//$this->getServer()->getCraftingManager()->buildCraftingDataCache();

		//foreach($this->getServer()->getOnlinePlayers() as $player){
			//$player->getNetworkSession()->sendDataPacket($this->getServer()->getCraftingManager()->getCraftingDataPacket());
		//}
	}
}