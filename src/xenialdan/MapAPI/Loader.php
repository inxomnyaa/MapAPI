<?php

namespace xenialdan\MapAPI;

use pocketmine\plugin\PluginBase;

class Loader extends PluginBase{

	public static $path = [];
	/** @var API */
	public static $mapUtils;

	public function onEnable(){
		self::$path['maps'] = $this->getDataFolder() . 'maps';
		self::$path['images'] = $this->getDataFolder() . 'images';
		self::$path['maps_exported'] = $this->getDataFolder() . 'maps_exported';
		//nbt maps
		@mkdir(self::$path['maps']);
		//png images
		@mkdir(self::$path['images']);
		//rescaled png images, just because i can :D
		@mkdir(self::$path['maps_exported']);
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->getServer()->getCommandMap()->register(Commands::class, new Commands($this));
		foreach (glob(self::$path['maps'].'map_*.dat') as $mapdata){
			$map = $this::getMapUtils()->loadFromNBT($mapdata);
			$this::getMapUtils()->cacheMap($map);
		}
		self::$mapUtils = new API();
	}

	public function onDisable(){
		foreach ($this::getMapUtils()->getAllCachedMaps() as $cachedMap){
			$cachedMap->save();
		}
	}

	public static function getMapUtils(){
		return self::$mapUtils;
	}
}