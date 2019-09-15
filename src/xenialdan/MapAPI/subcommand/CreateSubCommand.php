<?php

namespace xenialdan\MapAPI\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use xenialdan\MapAPI\API;
use xenialdan\MapAPI\item\Map;

class CreateSubCommand extends SubCommand
{

    public function canUse(CommandSender $sender)
    {
        try {
            return ($sender instanceof Player) and $sender->hasPermission("map.command.create");
        } catch (\InvalidStateException $e) {
            return false;
        }
    }

    public function getUsage(): string
    {
        return "create <png_filename>";
    }

    public function getName()
    {
        return "create";
    }

    public function getDescription()
    {
        return "Create a map from png";
    }

    public function getAliases()
    {
        return [];
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
        $png = str_replace(".png", "", $args[0]);
        if (($map = API::importFromPNG($png)) instanceof Map) {
            $player->sendMessage(TextFormat::GREEN . 'Map "' . $png . '", id:' . $map->getMapId() . ' received!');
            $player->getInventory()->addItem($map);
        } else {
            $player->sendMessage(TextFormat::RED . 'No file with the name "' . $png . '" exists in the MapAPI/images folder!');
        }
        return true;
    }
}
