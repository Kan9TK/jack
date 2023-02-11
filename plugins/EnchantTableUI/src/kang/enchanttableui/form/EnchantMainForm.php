<?php

declare(strict_types=1);

namespace kang\enchanttableui\form;

use jojoe77777\FormAPI\SimpleForm;
use kang\enchanttableui\EnchantTableUI;
use kang\enchanttableui\inventory\SelectItemInventory;
use kang\ServerUtils\ServerUtils;
use pocketmine\block\EnchantingTable;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class EnchantMainForm extends SimpleForm{

    protected array $level;

    public function __construct(protected Player $player, protected EnchantingTable $block){
        $plugin = EnchantTableUI::getInstance();
        $bookshelfCount = $plugin->getBookShelfCount($block);
        $this->level = [
            1 => (int)$plugin->getRandomLevel(1, $bookshelfCount),
            2 => (int)$plugin->getRandomLevel(2, $bookshelfCount),
            3 => (int)$plugin->getRandomLevel(3, $bookshelfCount)
        ];
        parent::__construct(function(Player $player, $data){
            if(!isset($data))return;

            if (
                $player->getXpManager()->getXpLevel() < $this->level[$data+1]
                ||
                $player->getInventory()->contains(ItemFactory::getInstance()->get(ItemIds::LAPIS_ORE,$data+1))
            ){
                ServerUtils::msg($player, "레벨이 부족하거나 청금석이 부족해요.");
                return;
            }

            $inv = new SelectItemInventory($player, $data+1, $this->level[$data+1]);
            $inv->send();
        });
        $this->setTitle("인첸트");
        $this->setContent([
            " ",
            " ",
            " "
        ]);

        for ( $i=1; $i<=3; $i++ ){
            if (
                $player->getXpManager()->getXpLevel() < $this->level[$i]
                ||
                $player->getInventory()->contains(ItemFactory::getInstance()->get(ItemIds::LAPIS_ORE,$i))
            ){
                $color = TextFormat::DARK_RED;
            }else{
                $color = "";
            }
            $this->addButton("{$color}필요: ".$this->level[$i]." XP | 소모: {$i}XP\n{$color}청금석 {$i}개 필요");
        }
    }

}