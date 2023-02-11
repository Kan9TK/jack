<?php

declare(strict_types=1);

namespace kang\itemeditor\form;

use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\item\Item;
use pocketmine\player\Player;

class MainForm extends SimpleForm{

    public function __construct(protected Item $item){
        parent::__construct(function(Player $player, $data){
            if(!isset($data))return;
            switch($data){
                case 0:
                    $player->sendForm($this->editNameForm());
                    break;
                case 1:
                    $player->sendForm($this->editLoreForm());
                    break;
                case 2:
                    $player->sendForm($this->editCountForm());
                    break;
            }
        });
        $this->setTitle("Item Editor");

        $name = $item->hasCustomName() ? $item->getCustomName() : $item->getName();
        $count = $item->getCount();
        $stringLore = (implode("\n", $item->getLore())) === "" ? "null" : (implode("\n", $item->getLore()));

        $this->setContent(" \n".$name." x".$count."\n".$stringLore."\n ");

        $this->addButton("이름 수정\n이름을 수정해요.");
        $this->addButton("로어 수정\n로어를 수정해요.");
        $this->addButton("갯수 수정\n갯수를 수정해요.");
    }
    
    public function editNameForm() : CustomForm{
        $form =  new CustomForm(
            function(Player $player, $data){
                if(!isset($data))return;
                $player->getInventory()->setItemInHand($this->item->setCustomName($data[0]));
            }
        );

        $name = $this->item->hasCustomName() ? $this->item->getCustomName() : $this->item->getName();

        $form->setTitle("이름 수정");
        $form->addInput("이름", "", $name);
        return $form;
    }

    public function editLoreForm() : CustomForm{
        $form =  new CustomForm(
            function(Player $player, $data){
                if(!isset($data))return;

                $data = array_filter($data, function($var){
                    return $var !== "";
                });

                $player->getInventory()->setItemInHand($this->item->setLore($data));
            }
        );
        $form->setTitle("로어 수정");
        for($i=0;$i<15;$i++){
            $lore = $this->item->getLore()[$i] ?? "";
            $form->addInput($i."", "", (string)$lore);
        }
        return $form;
    }

    public function editCountForm() : CustomForm{
        $form =  new CustomForm(
            function(Player $player, $data){
                if(!isset($data))return;
                $player->getInventory()->setItemInHand($this->item->setCount((int)$data[0]));
            }
        );
        $form->setTitle("갯수 수정");
        $form->addInput("갯수", "", (string)$this->item->getCount());
        return $form;
    }

}