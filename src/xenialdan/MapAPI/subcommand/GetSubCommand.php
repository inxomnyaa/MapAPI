<?php

namespace xenialdan\MapAPI\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\ClientboundMapItemDataPacket;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use xenialdan\MapAPI\API;
use xenialdan\MapAPI\Color;
use xenialdan\MapAPI\item\Map;
use xenialdan\MapAPI\Loader;

class GetSubCommand extends SubCommand{

	public function canUse(CommandSender $sender){
		return ($sender instanceof Player) and $sender->hasPermission("map.command.get");
	}

	public function getUsage(): string{
		return "get <id>";
	}

	public function getName(){
		return "get";
	}

	public function getDescription(){
		return "Get a map by id";
	}

	public function getAliases(){
		return [];
	}

	/**
	 * @param CommandSender $sender
	 * @param array $args
	 * @return bool
	 */
	public function execute(CommandSender $sender, array $args){
		$player = $sender->getServer()->getPlayer($sender->getName());
		if (count($args) < 1){
			$player->sendMessage(TextFormat::RED . 'Sorry, you need to provide an id for the map');
			return false;
		}
		$id = $args[0];
			$map = Loader::getMapUtils()->getCachedMap($id);
			$tag = new CompoundTag("", []);
			$tag->map_uuid = new StringTag("map_uuid", strval($id));
			$map->setCompoundTag($tag);
			$player->getInventory()->addItem($map);
			$map->update(ClientboundMapItemDataPacket::BITFLAG_TEXTURE_UPDATE);

		$player->sendMessage(TextFormat::GREEN . 'Map id:' . $id . ' received!');
		return true;
	}
}
