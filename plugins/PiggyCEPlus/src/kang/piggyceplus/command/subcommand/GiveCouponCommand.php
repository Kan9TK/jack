<?php

declare(strict_types=1);

namespace kang\piggyceplus\command\subcommand;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use kang\piggyceplus\util\CEBookUtil;
use kang\ServerUtils\ServerUtils;
use pocketmine\command\CommandSender;

class GiveCouponCommand extends BaseSubCommand{
    
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if ( count($args) < 1 ){
            return;
        }
        if ( isset ( $args["갯수"] ) && !is_numeric($args["갯수"]) ){
            return;
        }
        $coupons = array_values(CEBookUtil::COUPON_NAME);
        if ( ! in_array($args["종류"], $coupons) ){
            ServerUtils::error($sender, "주문서 종류: ".implode(", ", $coupons));
        }

        $count = intval($args["갯수"] ?? 1);

        switch ( $args["종류"] ){
            case CEBookUtil::COUPON_NAME[CEBookUtil::COUPON_PROTECT]:
                $coupon = CEBookUtil::createProtectCoupon();
                $coupon->setCount($count);
                $sender->getInventory()->addItem($coupon);
                ServerUtils::msg($sender, "성공적으로 지급되었어요.");
                break;
            case CEBookUtil::COUPON_NAME[CEBookUtil::COUPON_EXTRACT]:
                $coupon = CEBookUtil::createExTRACTCoupon();
                $coupon->setCount($count);
                $sender->getInventory()->addItem($coupon);
                ServerUtils::msg($sender, "성공적으로 지급되었어요.");
                break;
            case CEBookUtil::COUPON_NAME[CEBookUtil::COUPON_SLOT]:
                $coupon = CEBookUtil::createSlotCoupon();
                $coupon->setCount($count);
                $sender->getInventory()->addItem($coupon);
                ServerUtils::msg($sender, "성공적으로 지급되었어요.");
                break;
        }

    }
    
    protected function prepare(): void
    {
        $this->setPermission("op");
        $this->registerArgument(0, new RawStringArgument("종류"));
        $this->registerArgument(1, new IntegerArgument("갯수"));
    }

}