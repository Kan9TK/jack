<?php

declare(strict_types=1);

namespace alvin0319\BixbyMarket\command;

use alvin0319\BixbyMarket\BixbyMarket;
use alvin0319\BixbyMarket\command\subcommand\CategoryCommand;
use alvin0319\BixbyMarket\command\subcommand\CategoryCreateCommand;
use alvin0319\BixbyMarket\command\subcommand\CategoryEditCommand;
use alvin0319\BixbyMarket\command\subcommand\CategoryEditItemCommand;
use alvin0319\BixbyMarket\command\subcommand\MarketEditCommand;
use alvin0319\BixbyMarket\command\subcommand\CategorySetPageCommand;
use alvin0319\BixbyMarket\command\subcommand\ShopCreateCommand;
use alvin0319\BixbyMarket\command\subcommand\ShopCreateEntityCommand;
use alvin0319\BixbyMarket\command\subcommand\ShopDeleteCommand;
use alvin0319\BixbyMarket\command\subcommand\ShopListCommand;
use kang\CommandLibrary\BaseCommand;
use pocketmine\command\CommandSender;

class ManageMarketCommand extends BaseCommand{

    public function __construct(private BixbyMarket $plugin)
    {
        parent::__construct($plugin, "상점관리", "상점 관리 명령어입니다.");
    }

    public function prepare(): void
    {
        $this->setPermission("op");

        //$this->registerSubCommand(new CategoryCommand());

        $this->registerSubCommand(new ShopCreateCommand());
        $this->registerSubCommand(new ShopDeleteCommand());
        $this->registerSubCommand(new ShopListCommand());
        $this->registerSubCommand(new ShopCreateEntityCommand());

        $this->registerSubCommand(new CategoryCreateCommand());
        $this->registerSubCommand(new CategoryEditCommand());
        $this->registerSubCommand(new CategoryEditItemCommand());

        $this->registerSubCommand(new MarketEditCommand());
    }

    public function onExecute(CommandSender $player, string $commandLabel, array $args): bool
    {
        return true;
    }

}