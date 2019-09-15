<?php

namespace xenialdan\MapAPI\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\ClientboundMapItemDataPacket;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use xenialdan\MapAPI\Loader;

class GetSubCommand extends SubCommand
{

    public function canUse(CommandSender $sender)
    {
        try {
            return ($sender instanceof Player) and $sender->hasPermission("map.command.get");
        } catch (\InvalidStateException $e) {
            return false;
        }
    }

    public function getUsage(): string
    {
        return "get <id>";
    }

    public function getName()
    {
        return "get";
    }

    public function getDescription()
    {
        return "Get a map by id";
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
            $player->sendMessage(TextFormat::RED . 'Sorry, you need to provide an id for the map');
            return false;
        }
        $id = $args[0];
        $map = Loader::getMapUtils()->getCachedMap($id);
        try {
            $tag = new CompoundTag("", [new StringTag("map_uuid", strval($id))]);
            $map->setCompoundTag($tag);
        } catch (\InvalidArgumentException $e) {
        }
        $player->getInventory()->addItem($map);
        $map->update(ClientboundMapItemDataPacket::BITFLAG_TEXTURE_UPDATE);

        $player->sendMessage(TextFormat::GREEN . 'Map id:' . $id . ' received!');
        return true;
    }
}
