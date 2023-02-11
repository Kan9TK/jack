<?php
declare(strict_types=1);

namespace Texter;

use kang\positionutil\PositionUtil;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\Position;
use Texter\form\CreateForm;
use Texter\form\EditForm;
use Texter\form\MoveForm;
use Texter\form\RemoveForm;
use Texter\task\ShowTextTask;
use Texter\text\Text;

class Texter extends PluginBase implements Listener{
	use SingletonTrait;

	public static string $prefix = "§b§l[알림] §r§7";

	/** @var Text[] */
	protected array $text = [];

	protected function onLoad() : void{
		self::setInstance($this);
	}

	protected function onEnable() : void{
		$this->saveResource("config.yml");
		$this->getServer()->getPluginManager()->registerEvents($this, $this);

		$data = file_exists($this->getDataFolder() . "text.yml") ? yaml_parse(file_get_contents($this->getDataFolder() . "text.yml")) : [];

		foreach($data as $xyz => $string){
			$text = Text::jsonDeserialize($string);
			$this->text[$xyz] = $text;
		}

		if(file_exists($file = $this->getServer()->getDataPath() . "plugin_data/Tag/TagData.json")){
			$data = json_decode(file_get_contents($file), true);

			foreach($data as $xyz => $str){
				[, , , $world] = explode(":", $xyz);
				$this->text[$xyz] = new Text($str, self::toPosition($xyz), $world);
			}

		}

		$this->getScheduler()->scheduleRepeatingTask(new ShowTextTask($this->getConfig()->getNested("distance", 8)), 20);
	}

	protected function onDisable() : void{
		$this->save();
	}

	public function save() : void{
		$arr = [];
		foreach($this->text as $xyz => $text){
			$arr[$xyz] = $text->jsonSerialize();
		}
		file_put_contents($this->getDataFolder() . "text.yml", yaml_emit($arr, JSON_UNESCAPED_UNICODE));
	}

	public function addText(Position $pos, string $text){
		$t = new Text($text, $pos, $pos->getWorld()->getFolderName());
		$this->text[self::toString($pos)] = $t;
	}

	public function removeText(string $pos){
		$text = $this->text[$pos] ?? null;
		if($text instanceof Text){
			foreach(array_keys($text->hasSpawned) as $viewer){
				if(($target = $this->getServer()->getPlayerExact($viewer)) instanceof Player){
					$text->despawnTo($target);
				}
			}
			unset($this->text[$pos]);
		}
	}

	public function moveText(string $fromPos, Position $toPos) : void{
        $text = $this->text[$fromPos] ?? null;
        if($text instanceof Text){
            $text->setPosition($toPos);
            $this->text[self::toString($toPos)] = $text;
            unset($this->text[$fromPos]);
            $this->updateText($text);
        }
    }

    public function updateText(Text $text){
        if($text instanceof Text) {
            foreach (Server::getInstance()->getOnlinePlayers() as $player) {
                if ($player->isConnected() and $player->isAlive()) {
                    $text->despawnTo($player);
                    $text->spawnTo($player);
                }
            }
        }
    }

	public function getText(string $pos) : ?Text{
	    return $this->text[$pos] ?? null;
    }

	public static function toString(Position $pos) : string{
		return implode(":", [$pos->x, $pos->y, $pos->z, $pos->getWorld()->getFolderName()]);
	}

	public static function toPosition(string $str) : Position{
		[$x, $y, $z, $world] = explode(":", $str);

		return new Position((float) $x, (float) $y, (float) $z, Server::getInstance()->getWorldManager()->getWorldByName($world));
	}

	/**
	 * @return Text[]
	 */
	public function getTexts() : array{
		return array_values($this->text);
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if($sender instanceof Player){
			switch($args[0] ?? "x"){
				case "생성":
					$sender->sendForm(new CreateForm());
					break;
				case "제거":
					$sender->sendForm(new RemoveForm());
					break;
                case "수정":
                    $sender->sendForm(new EditForm());
                    break;
                case "이동":
                    $sender->sendForm(new MoveForm());
                    break;
				default:
					$sender->sendMessage(Texter::$prefix . "/texter 생성");
					$sender->sendMessage(Texter::$prefix . "/texter 제거");
                    $sender->sendMessage(Texter::$prefix . "/texter 수정");
                    $sender->sendMessage(Texter::$prefix . "/texter 이동");
			}
		}
		return true;
	}
}
