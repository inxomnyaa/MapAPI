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
use xenialdan\MapAPI\item\Map;

class EventListener implements Listener{
	/** @var Loader */
	public $owner;

	public function __construct(Plugin $plugin){
		$this->owner = $plugin;
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
				$path = Loader::$path['maps'] . '/map_' . $packet->mapId;
				if (!is_null($map = $this->owner::getMapUtils()->getCachedMap($packet->mapId))){
					$map->update(ClientboundMapItemDataPacket::BITFLAG_TEXTURE_UPDATE);
				} elseif ($packet->mapId == -1 || !file_exists($path)){
					$map = new Map($packet->mapId);
					$map->update(ClientboundMapItemDataPacket::BITFLAG_TEXTURE_UPDATE);
				} else{
					$map = $this->owner::getMapUtils()->loadFromNBT($packet->mapId);
					$map->update(ClientboundMapItemDataPacket::BITFLAG_TEXTURE_UPDATE);
					$this->owner::getMapUtils()->cacheMap($map);
				}
				break;
		}
	}

	public function onDisable(PluginDisableEvent $event){
		foreach ($this->owner::getMapUtils()->getAllCachedMaps() as $cachedMap){
			$this->owner::getMapUtils()->exportToNBT($cachedMap, $cachedMap->getMapId());
		}
	}
}