<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyCustomEnchants\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use DaPigGuy\PiggyCustomEnchants\CustomEnchantManager;
use DaPigGuy\PiggyCustomEnchants\enchants\CustomEnchant;
use DaPigGuy\PiggyCustomEnchants\PiggyCustomEnchants;
use DaPigGuy\PiggyCustomEnchants\utils\Utils;
use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class InfoSubCommand extends BaseSubCommand
{

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {

        $plugin = PiggyCustomEnchants::getInstance();

        if ($sender instanceof Player && $plugin->areFormsEnabled()) {
            if (isset($args["enchantment"])) {
                $enchantment = CustomEnchantManager::getEnchantmentByName($args["enchantment"]);
                if ($enchantment === null) {
                    Utils::errorForm($sender, TextFormat::RED . "Invalid enchantment.");
                    return;
                }
                $this->showInfo($sender, $enchantment);
                return;
            }
            $form = new CustomForm(function (Player $player, ?array $data) {
                if ($data !== null) {
                    $enchantment = CustomEnchantManager::getEnchantmentByName($data[0]);
                    if ($enchantment === null) {
                        Utils::errorForm($player, TextFormat::RED . "Invalid enchantment.");
                        return;
                    }
                    $this->showInfo($player, $enchantment);
                }
            });
            $form->setTitle("커스텀 인첸트 정보");
            $form->addInput("Enchantment");
            $sender->sendForm($form);
            return;
        }
        if (!isset($args["enchantment"])) {
            $sender->sendMessage("/ce info <enchantment>");
            return;
        }
        $enchantment = CustomEnchantManager::getEnchantmentByName($args["enchantment"]);
        if ($enchantment === null) {
            $sender->sendMessage(TextFormat::RED . "존재하지 않는 인첸트에요.");
            return;
        }
        $sender->sendMessage(TextFormat::GREEN . $enchantment->getDisplayName() . TextFormat::EOL . TextFormat::RESET . "ID: " . $enchantment->getId() . TextFormat::EOL . "Description: " . $enchantment->getDescription() . TextFormat::EOL . "Type: " . Utils::TYPE_NAMES[$enchantment->getItemType()] . TextFormat::EOL . "Rarity: " . Utils::RARITY_NAMES[$enchantment->getRarity()] . TextFormat::EOL . "Max Level: " . $enchantment->getMaxLevel());
    }

    public function showInfo(Player $player, CustomEnchant $enchantment): void
    {
        $infoForm = new SimpleForm(function (Player $player, ?int $data): void {
            if ($data !== null) PiggyCustomEnchants::getInstance()->getServer()->dispatchCommand($player, "ce");
        });
        $infoForm->setTitle($enchantment->getDisplayName());
        $infoForm->setContent([
            "아이디: " . $enchantment->getId(),
            "설명: " . $enchantment->getDescription(),
            "종류: " . Utils::TYPE_NAMES[$enchantment->getItemType()],
            "등급: " . Utils::RARITY_NAMES[$enchantment->getRarity()],
            "최대 레벨: " . $enchantment->getMaxLevel()
        ]);
        $infoForm->addButton("돌아가기");
        $player->sendForm($infoForm);
    }

    /**
     * @throws ArgumentOrderException
     */
    public function prepare(): void
    {
        $this->setPermission("piggycustomenchants.command.ce.list");
        $this->registerArgument(0, new RawStringArgument("enchantment", true));
    }
}