<?php

namespace xenialdan\MapAPI;

use pocketmine\event\Listener;
use pocketmine\event\plugin\PluginDisableEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\level\Level;
use pocketmine\network\mcpe\protocol\ClientboundMapItemDataPacket;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\MapInfoRequestPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\Server;

class EventListener implements Listener{
	/** @var MapUtils */
	private static $mapUtils;
	public $owner;

	public function __construct(Plugin $plugin){
		$this->owner = $plugin;
		self::$mapUtils = new MapUtils();
	}

	//TODO listen for packet, probably load from nbt/make new map
	public function onPacketReceive(DataPacketReceiveEvent $event){
		/** @var DataPacket $packet */
		if (!($packet = $event->getPacket()) instanceof MapInfoRequestPacket) return;
		/** @var Player $player */
		if (!($player = $event->getPlayer()) instanceof Player) return;
		/** @var Level $level */
		if (($level = $player->getLevel())->getId() !== Server::getInstance()->getDefaultLevel()->getId()){
			return;
		}
		/** @var MapInfoRequestPacket $packet */
		switch ($packet instanceof MapInfoRequestPacket){
			case ProtocolInfo::MAP_INFO_REQUEST_PACKET:
				/** @var MapInfoRequestPacket $packet */
				$path = Loader::$path['maps'] . '/map_' . $packet->uuid;
				if (!is_null($map = self::getMapUtils()->getCachedMap($packet->uuid))){
					$map->update(ClientboundMapItemDataPacket::BITFLAG_TEXTURE_UPDATE);
				} elseif ($packet->uuid == -1 || !file_exists($path)){
					$map = new Map($packet->uuid);
					$map->update(ClientboundMapItemDataPacket::BITFLAG_TEXTURE_UPDATE);
				} else{
					$map = self::getMapUtils()->loadFromNBT($packet->uuid);
					$map->update(ClientboundMapItemDataPacket::BITFLAG_TEXTURE_UPDATE);
					self::getMapUtils()->cacheMap($map);
				}
				break;
		}
	}

	public function onDisable(PluginDisableEvent $event){
		foreach (self::getMapUtils()->getAllCachedMaps() as $cachedMap){
			self::getMapUtils()->exportToNBT($cachedMap, $cachedMap->getMapId());
		}
	}

	public static function getMapUtils(){
		return self::$mapUtils;
	}
}