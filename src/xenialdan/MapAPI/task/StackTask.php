<?php

namespace xenialdan\ItemStacks\task;

use pocketmine\entity\Item;
use pocketmine\scheduler\PluginTask;
use pocketmine\Server;
use xenialdan\MapAPI\Loader;

class StackTask extends PluginTask {
	/** @var Loader $plugin */
	private $plugin;

	public function __construct(Loader $owner) {
		parent::__construct($owner);
		$this->plugin = $owner;
	}

	public function onRun($currentTick) {
		foreach (Server::getInstance()->getLevels() as $level) {
			foreach ($level->getEntities() as $entity) {
				if (!$entity instanceof Item || $entity->closed) continue;
				if ($entity->getItem()->getCount() >= $entity->getItem()->getMaxStackSize()) continue;
				if (empty($entities = $level->getNearbyEntities($entity->getBoundingBox()->grow(1, 1, 1), $entity))) continue;
				else {
					foreach ($entities as $possibleItem) {
						if (!$possibleItem instanceof Item || $possibleItem->closed) continue;
						if ($possibleItem->getItem()->getCount() > $possibleItem->getItem()->getMaxStackSize()) continue;
						if ($entity->getItem()->equals($possibleItem->getItem(), true, true)) {
							if (($newCount = $entity->getItem()->getCount() + $possibleItem->getItem()->getCount()) >= $entity->getItem()->getMaxStackSize()) continue;
							//stack
							$this->plugin->getLogger()->debug('Stacked ' . $entity->getItem() . ' with ' . $possibleItem->getItem());
							$entity->getItem()->setCount($newCount);
							$this->plugin->getLogger()->debug('got item ' . $entity->getItem());
							$possibleItem->close();
						}
					}
				}
			}
		}
	}

	public function cancel() {
		$this->getHandler()->cancel();
	}
}