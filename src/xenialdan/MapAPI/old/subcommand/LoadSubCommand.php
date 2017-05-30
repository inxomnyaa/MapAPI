<?php

namespace xenialdan\MapUtils\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\item\Item;
use pocketmine\item\Map;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\ClientboundMapItemDataPacket;
use pocketmine\Player;
use pocketmine\utils\Color;
use pocketmine\utils\Config;
use pocketmine\utils\MapUtils;
use pocketmine\utils\TextFormat;

class LoadSubCommand extends SubCommand
{

    public function canUse(CommandSender $sender)
    {
        return ($sender instanceof Player) and $sender->hasPermission("map.command.load");
    }

    public function getUsage()
    {
        return "load <png_filename>";
    }

    public function getName()
    {
        return "load";
    }

    public function getDescription()
    {
        return "Get a map from png";
    }

    public function getAliases()
    {
        return ["get"];
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, array $args)
    {
        $player = $sender->getServer()->getPlayer($sender->getName());
        if (count($args) < 1) {
            $player->sendMessage(TextFormat::RED . 'Sorry, you need to provide a png filename for the map');
            return false;
        }
        $png = $args[0];
        $mapUtils = new MapUtils();
        $colors = [];
        $image = @imagecreatefrompng($this->getPlugin()->getDataFolder() . 'png_maps/' . $args[0] .'.png');
        if ($image !== false) {
			$ratio = imagesx($image)/imagesy($image);
			if( $ratio > 1) {
				$width = 128;
				$height = 128/$ratio;
			}
			else {
				$width = 128*$ratio;
				$height = 128;
			}
			$image = imagescale($image, $width, $height, IMG_NEAREST_NEIGHBOUR);
			imagepng($image, $this->getPlugin()->getDataFolder() . 'png_maps/' . $args[0] .'.new.png');
			for ($y = 0; $y < $height; ++$y) {
				for ($x = 0; $x < $width; ++$x) {
					$color = imagecolorsforindex($image, imagecolorat($image, $x, $y));
					$colors[$y][$x] = $mapUtils->getClosestMapColor(new Color($color['red'],$color['green'],$color['blue'],$color['alpha']));
				}
			}
			$map = new Map($id = MapUtils::getNewId(), $colors, 1, $width, $height);
			$item = Item::get(Item::FILLED_MAP, 0, 1);
			$tag = new CompoundTag("", []);
			$tag->map_uuid = new StringTag("map_uuid", strval($id));
			$item->setCompoundTag($tag);
			$player->getInventory()->addItem($item);
			$map->update(ClientboundMapItemDataPacket::BITFLAG_TEXTURE_UPDATE);
			MapUtils::cacheMap($map);
        } else {
            $player->sendMessage(TextFormat::RED . 'Wasn\'t able to create or access the png file! Make sure your path is correct!');
            return false;
        }
        $player->sendMessage(TextFormat::GREEN . 'Map "' . $png . '", id:'.$id.' received!');
        return true;
    }
}
