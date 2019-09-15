<?php

namespace xenialdan\MapAPI;

use pocketmine\item\ItemFactory;
use pocketmine\plugin\PluginBase;
use pocketmine\plugin\PluginException;
use xenialdan\MapAPI\item\Map;

class Loader extends PluginBase
{

    /** @var Loader */
    private static $instance = null;
    public static $path = [];
    /** @var API */
    public static $mapUtils;

    /**
     * Returns an instance of the plugin
     * @return Loader
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    public function onLoad()
    {
        self::$instance = $this;
    }

    public function onEnable()
    {
        self::$path['maps'] = $this->getDataFolder() . 'maps';
        self::$path['images'] = $this->getDataFolder() . 'images';
        self::$path['maps_exported'] = $this->getDataFolder() . 'maps_exported';
        //nbt maps
        @mkdir(self::$path['maps']);
        //png images
        @mkdir(self::$path['images']);
        //map data to img
        @mkdir(self::$path['maps_exported']);
        try {
            $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        } catch (PluginException $e) {
        }
        $this->getServer()->getCommandMap()->register(Commands::class, new Commands($this));
        self::$mapUtils = new API();
        try {
            ItemFactory::registerItem(new Map(), true);
        } catch (\RuntimeException $e) {
        }
        foreach (glob(self::$path['maps'] . '/*.dat') as $mapdata) {
            $map = $this::getMapUtils()->loadFromNBT($mapdata);
            $this::getMapUtils()->cacheMap($map);
        }
    }

    public function onDisable()
    {
        foreach ($this::getMapUtils()->getAllCachedMaps() as $cachedMap) {
            $cachedMap->save();
        }
    }

    /**
     * @return API
     */
    public static function getMapUtils()
    {
        return self::$mapUtils;
    }
}