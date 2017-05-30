<?php

namespace xenialdan\MapAPI;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Loader extends PluginBase{

	public static $path = [];

	public function onEnable(){
		self::$path['maps'] = $this->getDataFolder().'maps';
		self::$path['images'] = $this->getDataFolder().'images';
		self::$path['maps_exported'] = $this->getDataFolder().'maps_exported';
		@mkdir(self::$path['maps']);
		@mkdir(self::$path['images']);
		@mkdir(self::$path['maps_exported']);
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->getServer()->getCommandMap()->register(Commands::class, new Commands($this));
	}
}