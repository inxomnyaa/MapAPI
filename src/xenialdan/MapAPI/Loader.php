<?php

namespace xenialdan\MapAPI;

use pocketmine\item\ItemFactory;
use pocketmine\plugin\PluginBase;
use xenialdan\MapAPI\item\Map;

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
		//map data to img
		@mkdir(self::$path['maps_exported']);
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->getServer()->getCommandMap()->register(Commands::class, new Commands($this));
		self::$mapUtils = new API();
		ItemFactory::registerItem(new Map(), true);
		foreach (glob(self::$path['maps'] . '/*.dat') as $mapdata){
			$map = $this::getMapUtils()->loadFromNBT($mapdata);
			$this::getMapUtils()->cacheMap($map);
		}
	}

	public function onDisable(){
		foreach ($this::getMapUtils()->getAllCachedMaps() as $cachedMap){
			$cachedMap->save();
		}
	}

	/**
	 * @return API
	 */
	public static function getMapUtils(){
		return self::$mapUtils;
	}
}