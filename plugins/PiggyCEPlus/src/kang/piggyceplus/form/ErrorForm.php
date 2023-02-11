<?php

declare(strict_types=1);

namespace kang\piggyceplus\form;

use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;

class ErrorForm extends SimpleForm{
    
    public function __construct(string|array $msg)
    {
        
        if ( is_array($msg) ){
            $msg = implode("\n", $msg);
        }
        
        parent::__construct(function(Player $player, $data){
            if(!isset($data))return;
            
        });
        
        $this->setTitle("특수 인첸트");
        $this->setContent($msg);
        
    }

}