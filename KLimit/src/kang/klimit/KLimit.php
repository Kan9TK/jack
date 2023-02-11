<?php

declare(strict_types=1);
	
namespace kang\klimit;

use kang\klimit\command\MainCommand;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\nbt\tag\StringTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerItemHeldEvent;

class KLimit extends PluginBase implements Listener{
	
	public Config $a;
	public array $data;
	
	public function onEnable(): void{
		$this->getServer()->getCommandMap()->registerAll($this->getName(), [
            new MainCommand($this)
        ]);
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->a = new Config($this->getDataFolder() . 'Data.yml', Config::YAML);
        $this->data = $this->a->getAll();
    }
	
	public function onHeld(PlayerItemHeldEvent $event) : void{
		$player = $event->getPlayer();
		$item = $player->getInventory()->getItemInHand();
		
		if($item->getNamedTag()->getTag('limit') instanceof IntTag){
			if( ! $item->getNamedTag()->getTag('used') instanceof StringTag){
				$item->getNamedTag()->setString('used', '');
				$item->getNamedTag()->setInt('limit', time() + $item->getNamedTag()->getTag('limit')->getValue());
				
				$time = time() + $item->getNamedTag()->getTag('limit')->getValue();
				
				$lore = $item->getLore();
				$lore[count($lore)] = "§a( §f기간제: §e".date("Y-m-d h:i:s", $time)." §f까지 )";
				
				$player->getInventory()->setItemInHand($item);
				$player->sendMessage("해당 기간제 아이템이 사용되었습니다.");
				return;
			}
		}
		
		$this->checkTime($item);
		
	}
	
	public function checkTime($item) : void{
		if($item->getNamedTag()->getTag('limit') instanceof IntTag){
			
			if($item->getNamedTag()->getTag('used') instanceof StringTag){
				$time = $item->getNamedTag()->getTag('limit')->getValue();
				
				if($time < time()){
                $player->getInventory()->removeItem($item);
                $this->owner->msg($player, '해당 아이템의 기간이 만료되었습니다.');
				}
				
			}
			
		}
		
	}

    public function onDisable() : void
    {
        $this->a->setAll($this->data);
        $this->a->save();
    }
	
}