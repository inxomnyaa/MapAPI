<?php

namespace xenialdan\MapAPI;

use pocketmine\event\Listener;
use pocketmine\plugin\Plugin;

class EventListener implements Listener{
	public $owner;

	public function __construct(Plugin $plugin){
		$this->owner = $plugin;
	}
}