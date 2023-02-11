<?php

declare(strict_types=1);

namespace kang\klimit\command;

use pocketmine\command\Command;

class MainCommand extends Command{
	
	public function __construct(protected KLimit $plugin)
    {
        parent::__construct('기간제관리', '기간제 아이템을 관리합니다.');
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
		if(!$sender->isOp()){
			return true;
		}
		if(!isset($args[0])){
			$sender->sendMessage("/기관제관리 아이템추가 [별명] [일] [시] [분] [초]");
			$sender->sendMessage("/기관제관리 아이템목록");
			return true;
		}
		switch($args[0]){
			case "아이템추가":
				
				$item = $sender->getInventory()->getItemInHand();
				
				if($item->getId() === 0){
					$sender->sendMessage("공기는 불가능합니다.");
					return true;
				}
				
				if(!isset($args[0],$args[1],$args[2],$args[3],$args[4])){
					$sender->sendMessage("/기관제관리 아이템추가 [별명] [일] [시] [분] [초]");
					return true;
				}
				if(!is_numeric($args[1]) or !is_numeric($args[2]) or !is_numeric($args[3]) or is_numeric($args[4]) ){
					$sender->sendMessage("/기관제관리 아이템추가 [별명] [일] [시] [분] [초]");
					return true;
				}
				
				$name = $args[0];
				$d = $args[1];
				$h = $args[2];
				$m = $args[3];
				$s = $args[4];
				
				$time = $d*86400 + $h*3600 + $m*60 + $s;
				
				$lore = $item->getLore();
				$lore[] = "§a( §f기간제 아이템: §e".$d."일 ".$h."시간 ".$m."분 ".$s."초 §a)";
				
				$item->setLore($lore);
				$item->getNamedTag()->setInt('limit', $time);
				
				$this->plugin->data[$name]["nbt"] = $item->jsonSerialize();
				$this->plugin->data[$name]["d"] = $d;
				$this->plugin->data[$name]["h"] = $h;
				$this->plugin->data[$name]["m"] = $m;
				$this->plugin->data[$name]["s"] = $s;
				
				$sender->getInventory()->addItem($item);
				
				$sender->sendMessage("성공적으로 추가했습니다.");
				
				break;
			case "아이템목록":
				
				$sender->sendForm(new ListForm($this->plugin));
				
				break;
		}
	}
	
	
}

?>