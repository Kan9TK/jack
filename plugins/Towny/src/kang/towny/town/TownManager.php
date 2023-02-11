<?php

declare(strict_types=1);

namespace kang\towny\town;

use kang\dataconfig\DataConfig;
use kang\towny\town\Town;
use kang\towny\Towny;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class TownManager{

    private array $towns = [];
    private DataConfig $tax;

    public function __construct(private Towny $plugin){
        $this->loadAllTown();
        $this->tax = new DataConfig($this->plugin->getDataFolder()."tax.json",DataConfig::TYPE_JSON,["lastDay"=>date("d")]);
    }

    public function loadTown(Town $town) : void{
        $this->towns[$town->getOwner()] = $town;
    }

    public function loadAllTown() : void{

        if ( ! is_dir($this->plugin->getDataFolder() . "towns") ){
            mkdir($this->plugin->getDataFolder() . "towns");
        }

        foreach(array_diff(scandir($this->plugin->getDataFolder() . "towns/"), [".", ".."]) as $file){
            $content = file_get_contents($this->plugin->getDataFolder() . "towns/".$file);
            $townData = json_decode($content, true);
            $town = Town::jsonDeserialize($townData);
            $this->loadTown($town);
        }
    }

    public function unloadTown(Town $town) : void{
        if ( isset ( $this->towns[$town->getOwner()] ) ){
            unset($this->towns[$town->getOwner()]);
        }
    }

    public function deleteTown(Town $town) : void{
        if ( file_exists($file = $this->plugin->getDataFolder()."towns/".$town->getOwner().".json") ){
            unlink($file);
        }
        $this->unloadTown($town);
    }

    public function updateTaxLastDay() : void{
        $this->tax->set("lastDay", date("d"));
    }

    public function getTownByName(string $playerName) : ?Town{
        if ( isset ( $this->towns[$playerName] ) ){
            return $this->towns[$playerName];
        }else{
            foreach ( $this->towns as $ownerName => $town ) {
                if ($town instanceof Town) {
                    if ( $town->isMember($playerName) ){
                        return $town;
                    }
                }
            }
        }
        return null;
    }

    public function getTownByPos(Vector3 $vector3) : ?Town{
        foreach ( $this->towns as $playerName => $town ){
            if ( $town instanceof Town){
                if ( $vector3->x >= $town->getMinX() && $vector3->x <= $town->getMaxX() && $vector3->z >= $town->getMinZ() && $vector3->z <= $town->getMaxZ() ){
                    return $town;
                }
            }
        }
        return null;
    }

    public function getAllTowns() : array{
        return $this->towns;
    }

    public function getTaxLastDay() : int{
        return intval($this->tax->get("lastDay"));
    }

    public function save() : void{
        foreach ( $this->getAllTowns() as $playerName => $town  ){
            if ( $town instanceof Town ){
                $townData = $town->jsonSerialize();
                $content = json_encode($townData, JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING);
                file_put_contents($this->plugin->getDataFolder()."towns/".$playerName.".json", $content);
            }
        }
        $this->tax->save($this->tax->data);
    }

}