<?php
declare(strict_types=1);

namespace Texter\task;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use Texter\Texter;

class ShowTextTask extends Task{

    public function __construct(protected int $distance){}

	public function onRun() : void{

		foreach(Texter::getInstance()->getTexts() as $text){

			if($text->hasValidPos()){

				foreach(Server::getInstance()->getOnlinePlayers() as $player){

					if($player->isConnected() and $player->isAlive()){

						if(
						    ($text->distance($player->getPosition()) <= $this->distance)
                            &&
                            $player->getWorld()->getFolderName() === $text->getPosition()->getWorld()->getFolderName()
                        ){

							$text->spawnTo($player);

						}else{
							$text->despawnTo($player);
						}
					}
				}
			}
		}
	}
}