<?php

namespace xenialdan\ItemStacks\task;

use pocketmine\entity\Item;
use pocketmine\scheduler\PluginTask;
use pocketmine\Server;
use xenialdan\ItemStacks\Loader;

class AsyncGenerateTask extends PluginTask {
	/** @var Loader $plugin */
	private $plugin;

	public function __construct(Loader $owner) {
		parent::__construct($owner);
		$this->plugin = $owner;
	}

	public function onRun($currentTick) {
	}

	public function cancel() {
		$this->getHandler()->cancel();
	}
}