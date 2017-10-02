<?php

namespace xenialdan\MapAPI;

use pocketmine\event\level\LevelSaveEvent;
use pocketmine\event\Listener;
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

	public function onSaveEvent(LevelSaveEvent $event){
		foreach ($this->owner::getMapUtils()->getAllCachedMaps() as $cachedMap){
			$cachedMap->save();
		}
	}

	//TODO listen for packet, probably load from nbt/make new map
	public function onPacketReceive(DataPacketReceiveEvent $event){
		/** @var DataPacket $packet */
		if (!($packet = $event->getPacket()) instanceof MapInfoRequestPacket&&!$packet instanceof ClientboundMapItemDataPacket) return;
		/** @var Player $player */
		if (!($player = $event->getPlayer()) instanceof Player) return;
		/** @var MapInfoRequestPacket $packet */
		switch ($packet::NETWORK_ID){
			case MapInfoRequestPacket::NETWORK_ID:
				/** @var MapInfoRequestPacket $packet */
				$path = Loader::$path['maps'] . '/map_' . $packet->mapId;
				if (!is_null($map = $this->owner::getMapUtils()->getCachedMap($packet->mapId))){
					$map->update(ClientboundMapItemDataPacket::BITFLAG_TEXTURE_UPDATE);
				} elseif ($packet->mapId == -1 || !file_exists($path)){
					$map = new Map($packet->mapId);
					$map->update(ClientboundMapItemDataPacket::BITFLAG_TEXTURE_UPDATE);
					$this->owner::getMapUtils()->cacheMap($map);
				} else{
					$map = $this->owner::getMapUtils()->loadFromNBT($packet->mapId);
					$map->update(ClientboundMapItemDataPacket::BITFLAG_TEXTURE_UPDATE);
					$this->owner::getMapUtils()->cacheMap($map);
				}
				$event->setCancelled();
				break;
			case ClientboundMapItemDataPacket::NETWORK_ID:{
				$event->setCancelled();
				break;
			}
		}
	}
}