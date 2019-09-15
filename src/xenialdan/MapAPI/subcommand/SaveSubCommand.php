<?php

namespace xenialdan\MapAPI\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use xenialdan\MapAPI\API;
use xenialdan\MapAPI\Loader;

class SaveSubCommand extends SubCommand
{

    public function canUse(CommandSender $sender)
    {
        try {
            return ($sender instanceof Player) and $sender->hasPermission("map.command.save");
        } catch (\InvalidStateException $e) {
            return false;
        }
    }

    public function getUsage(): string
    {
        return "save";
    }

    public function getName()
    {
        return "save";
    }

    public function getDescription()
    {
        return "Saves all maps";
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
        foreach (Loader::getMapUtils()->getAllCachedMaps() as $cachedMap) {
            $cachedMap->save();
            if (!API::exportToPNG($cachedMap)) {
                $sender->sendMessage(TextFormat::RED . "Map with id {$cachedMap->getId()} could not be saved as png");
            }
        };
        $sender->sendMessage(TextFormat::GREEN . 'All maps should be saved to NBT now.');
        return true;
    }
}
