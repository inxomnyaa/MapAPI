<?php
namespace xenialdan\MapUtils;

use pocketmine\command\PluginCommand;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use xenialdan\MapUtils\subcommand\LoadSubCommand;
use xenialdan\MapUtils\subcommand\SubCommand;

class Commands extends PluginCommand
{
    private $subCommands = [];

    /* @var SubCommand[] */
    private $commandObjects = [];

    public function __construct(Plugin $plugin) {
        parent::__construct("map", $plugin);
        $this->setPermission("map.command");
        $this->setDescription("The main commands for MapAPI");
        $this->loadSubCommand(new LoadSubCommand($plugin));
    }

    private function loadSubCommand(SubCommand $command) {
        $this->commandObjects[] = $command;
        $commandId = count($this->commandObjects) - 1;
        $this->subCommands[$command->getName()] = $commandId;
        foreach ($command->getAliases() as $alias) {
            $this->subCommands[$alias] = $commandId;
        }
    }

    public function execute(CommandSender $sender, $alias, array $args) {
        if (!isset($args[0])) {
            return $this->sendHelp($sender);
        }
        $subCommand = strtolower(array_shift($args));
        if (!isset($this->subCommands[$subCommand])) {
            return $this->sendHelp($sender);
        }
        $command = $this->commandObjects[$this->subCommands[$subCommand]];
        $canUse = $command->canUse($sender);
        if ($canUse) {
            if (!$command->execute($sender, $args)) {
				$sender->sendMessage(TextFormat::YELLOW . "Usage: /map " . $command->getName() . TextFormat::BOLD . TextFormat::DARK_AQUA . " > " . TextFormat::RESET . TextFormat::YELLOW . $command->getUsage());
            }
        } elseif (!($sender instanceof Player)) {
            $sender->sendMessage(TextFormat::RED . "Please run this command in-game.");
        } else {
            $sender->sendMessage(TextFormat::RED . "You do not have permissions to run this command");
        }
        return true;
    }

    private function sendHelp(CommandSender $sender) {
        $sender->sendMessage("===========[MapAPI commands]===========");
        foreach ($this->commandObjects as $command) {
            if ($command->canUse($sender)) {
                $sender->sendMessage(TextFormat::DARK_GREEN . "/map " . $command->getName() . TextFormat::BOLD . TextFormat::DARK_AQUA . " > " . TextFormat::RESET . TextFormat::DARK_GREEN . $command->getUsage() . ": " .
                    TextFormat::WHITE . $command->getDescription()
                );
            }
        }
        return true;
    }
}
