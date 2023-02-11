<?php

declare(strict_types=1);

namespace slapper;

use pocketmine\command\CommandSender;
use pocketmine\lang\Language;
use pocketmine\lang\Translatable;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\PermissibleBase;
use pocketmine\permission\PermissibleDelegateTrait;
use pocketmine\Server;

class SlapperCommandSender implements CommandSender{
    use PermissibleDelegateTrait;

    protected ?int $lineHeight = null;

    private Server $server;
    private Language $language;

    public function __construct(private Main $plugin){
        $this->server = $plugin->getServer();
        $this->language = $this->server->getLanguage();
        $this->perm = new PermissibleBase([DefaultPermissions::ROOT_OPERATOR => true]);
    }

    public function getServer() : Server{
        return $this->server;
    }

    public function getLanguage() : Language{
        return $this->language;
    }

    public function sendMessage(Translatable|string $message) : void{
        if($message instanceof Translatable){
            $message = $this->language->translate($message);
        }

        $logger = $this->plugin->getLogger();
        foreach(explode("\n", trim($message)) as $line){
            $logger->info($line);
        }
    }

    public function getName() : string{
        return $this->plugin->getName();
    }

    public function getScreenLineHeight() : int{
        return $this->lineHeight ?? PHP_INT_MAX;
    }

    public function setScreenLineHeight(?int $height) : void{
        if($height !== null and $height < 1){
            throw new \InvalidArgumentException("Line height must be at least 1");
        }
        $this->lineHeight = $height;
    }

	public function hasPermission($name) : bool{
		return true;
	}
}