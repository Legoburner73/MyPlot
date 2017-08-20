<?php
/**
 * Created by PhpStorm.
 * User: funtimes
 * Date: 8/18/17
 * Time: 1:40 PM
 */

namespace MyPlot\subcommand;

use MyPlot\task\SharePlotTask;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class ShareSubCommand extends SubCommand {
	public function getName() {
		return "share";
	}

	public function getDescription() {
		return "Allows you to share your plot online";
	}

	public function getAliases() {
		return ["shr"];
	}

	public function canUse(CommandSender $sender) {
		return ($sender instanceof Player) and $sender->hasPermission("myplot.command.share");
	}

	public function execute(CommandSender $sender, array $args) {

		$player = $sender->getServer()->getPlayer($sender->getName());
		$plot = $this->getPlugin()->getPlotByPosition($player->getPosition());

		if ($plot === null) {
			$sender->sendMessage(TextFormat::RED . "You are not standing inside a plot");
			return true;
		}

		if ($plot->owner !== $sender->getName()) {
			$sender->sendMessage(TextFormat::RED . "You are not the owner of this plot");
			return true;
		}

		$task = new SharePlotTask($this->getPlugin(), $player, $plot, $args);
		$this->getPlugin()->getServer()->getScheduler()->scheduleRepeatingTask($task, 0);

		$player->sendMessage("Sharing plot...");

		return true;
	}

	public  function getUsage() {
		return "[name]";
	}
}