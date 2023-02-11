<?php

declare(strict_types=1);

namespace kang\towny\setting;

use kang\towny\Towny;

class Setting{

    public const WORLD_NAME = "town";

    public const CREAT_PRICE = 5000;

    public const DEFAULT_SIZE = 10;

    public const INCREASE_PRICE = 5000;
    public const INCREASE_SIZE = 10;

    public const BOARD_PRICE = 5000;
    public const BOARD_COOL = 60*60*12; // 12시간

    public const MAINTENANCE_PEOPLE_PRICE = 5000;
    public const MAINTENANCE_INCREASE_PRICE = 5000;

    public const TAX_TIME = 12;

    public function __construct(private Towny $plugin){
    }

}