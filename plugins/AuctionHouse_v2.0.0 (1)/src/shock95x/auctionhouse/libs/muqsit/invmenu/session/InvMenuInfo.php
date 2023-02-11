<?php

declare(strict_types=1);

namespace shock95x\auctionhouse\libs\muqsit\invmenu\session;

use shock95x\auctionhouse\libs\muqsit\invmenu\InvMenu;
use shock95x\auctionhouse\libs\muqsit\invmenu\type\graphic\InvMenuGraphic;

final class InvMenuInfo{

	public function __construct(
		public InvMenu $menu,
		public InvMenuGraphic $graphic
	){}
}