<?php

namespace xenialdan\MapUtils;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Loader extends PluginBase{

	public function onEnable(){
		#$this->saveDefaultConfig();//it has no config yet
		#$this->getConfig()->save();
		@mkdir($this->getDataFolder().'png_maps');
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getCommandMap()->register(Commands::class, new Commands($this));
	}
}