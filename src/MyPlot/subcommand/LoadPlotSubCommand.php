<?php
/**
 * Created by PhpStorm.
 * User: funtimes
 * Date: 8/18/17
 * Time: 6:15 PM
 */

namespace MyPlot\subcommand;

use MyPlot\task\LoadPlotTask;
use pocketmine\block\Block;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\Player;

class LoadPlotSubCommand extends SubCommand {
	public function getName() {
		return "loadplot";
	}

	public function getDescription() {
		return "Allows you to load a plot from the online repository";
	}

	public function getAliases() {
		return ["ld"];
	}

	public function canUse(CommandSender $sender) {
		return ($sender instanceof Player) and $sender->hasPermission("myplot.command.loadplot");
	}

	public function execute(CommandSender $sender, array $args) {
		$player = $sender->getServer()->getPlayer($sender->getName());
		$plot = $this->getPlugin()->getPlotByPosition($player->getPosition());

		if ($plot === null) {
			$sender->sendMessage(TextFormat::RED . "You are not standing inside a plot");
			return true;
		}

		if ($plot->owner !== $sender->getName() and !$sender->hasPermission("myplot.admin.loadplot")) {
			$sender->sendMessage(TextFormat::RED . "You are not the owner of this plot");
			return true;
		}

		if (count($args) == 2) {
			$filePath = "plots/" . mb_strtolower($args[0]) . "/" . $args[1] . ".json.gz";

			if (is_file($filePath)) {
				$task = new LoadPlotTask($this->getPlugin(), $player, $filePath, $plot);

				$this->getPlugin()->getServer()->getScheduler()->scheduleRepeatingTask($task, 0);

				$player->sendMessage("Plot found and loading...");

				return true;
			} else {
				$sender->sendMessage(TextFormat::RED . "A plot with that ID could not be found for the specified user.");
				return false;
			}
		}

		return false;
	}

	public  function getUsage() {
		return "<player name> <plot id>";
	}
}