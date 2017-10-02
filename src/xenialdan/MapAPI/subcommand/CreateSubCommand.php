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

class CreateSubCommand extends SubCommand{

	public function canUse(CommandSender $sender){
		return ($sender instanceof Player) and $sender->hasPermission("map.command.create");
	}

	public function getUsage(): string{
		return "create <png_filename>";
	}

	public function getName(){
		return "create";
	}

	public function getDescription(){
		return "Create a map from png";
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
			$player->sendMessage(TextFormat::RED . 'Sorry, you need to provide a png filename for the map');
			return false;
		}
		$png = str_replace(".png", "", $args[0]);
		$colors = [];
		$image = @imagecreatefrompng($this->getPlugin()->getDataFolder() . 'images/' . $png . '.png');
		if ($image !== false){
			$ratio = imagesx($image) / imagesy($image);
			if ($ratio > 1){
				$width = 128;
				$height = 128 / $ratio;
			} else{
				$width = 128 * $ratio;
				$height = 128;
			}
			$image = imagescale($image, $width, $height, IMG_NEAREST_NEIGHBOUR);
			$width = imagesx($image);
			$height = imagesy($image);
			#imagepng($image, $this->getPlugin()->getDataFolder() . 'maps_exported/' . $args[0] . '.png');
			for ($y = 0; $y < $height; ++$y){
				for ($x = 0; $x < $width; ++$x){
					$color = imagecolorsforindex($image, imagecolorat($image, $x, $y));
					$colors[$y][$x] = /*Loader::getMapUtils()->getClosestMapColor(*/
						new Color($color['red'], $color['green'], $color['blue']/*, $color['alpha']*/)/*)*/
					;
				}
			}
			$map = new Map($id = API::getNewId(), $colors, 2, $height, $width);
			$tag = new CompoundTag("", []);
			$tag->map_uuid = new StringTag("map_uuid", strval($id));
			$map->setCompoundTag($tag);
			$player->getInventory()->addItem($map);
			$map->update(ClientboundMapItemDataPacket::BITFLAG_TEXTURE_UPDATE);
			Loader::getMapUtils()->cacheMap($map);
		} else{
			$player->sendMessage(TextFormat::RED . 'Wasn\'t able to create or access the png file! Make sure your path is correct!');
			return false;
		}
		$player->sendMessage(TextFormat::GREEN . 'Map "' . $png . '", id:' . $id . ' received!');
		return true;
	}
}
