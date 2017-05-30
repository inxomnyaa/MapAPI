<?php

namespace xenialdan\MapAPI;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Loader extends PluginBase{

	public function onEnable(){
		@mkdir($this->getDataFolder().'images');
		@mkdir($this->getDataFolder().'maps');
		@mkdir($this->getDataFolder().'maps_exported');
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getCommandMap()->register(Commands::class, new Commands($this));
	}
}