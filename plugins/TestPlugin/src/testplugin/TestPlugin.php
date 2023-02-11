<?php
declare(strict_types=1);

namespace testplugin;

use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class TestPlugin extends PluginBase{

    public array $test1 = [];
    public static array $test2 = [];
    private static TestPlugin $instnace;

    public function onLoad(): void{
        self::$instnace = $this;
    }

    public function onEnable(): void{
    }

    public function test1(){
        $startTime = microtime(true);
        $this->test11();
        $endTime = microtime(true);
        $diff = $endTime - $startTime;
        var_dump("1: ".$diff);
    }

    public function test11(){
        for($i=0;$i<500000;$i++){
            $test = $this->test1;
        }
    }

    public function test2(){
        $startTime = microtime(true);
        self::test22();
        $endTime = microtime(true);
        $diff = $endTime - $startTime;
        var_dump("2: ".$diff);
    }

    public static function test22(){
        for($i=0;$i<500000;$i++){
            $test = self::$test2;
        }
    }

}