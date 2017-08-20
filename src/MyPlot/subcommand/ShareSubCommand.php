<?php
/**
 * Created by PhpStorm.
 * User: funtimes
 * Date: 8/18/17
 * Time: 1:40 PM
 */

namespace MyPlot\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class ShareSubCommand extends SubCommand
{
	public function getName()
	{
		return "share";
	}

	public function getDescription()
	{
		return "Allows you to share your plot online";
	}

	public function getAliases() {
		return ["shr"];
	}

	public function canUse(CommandSender $sender) {
		return ($sender instanceof Player) and $sender->hasPermission("myplot.command.share");
	}

	public  function execute(CommandSender $sender, array $args) {

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

		$blockPositions = [];

		$plotSize = $this->getPlugin()->getLevelSettings($plot->levelName)->plotSize;
		$height = 256; // hard-coding this should be fine, for now, and for performance

		$plotPosition = $this->getPlugin()->getPlotPosition($plot);

		for ($x = 0; $x < $plotSize; $x++) {
			for ($z = 0; $z < $plotSize; $z++) {
				for ($y = 0; $y < $height; $y++) {

					$position = new Vector3($x + $plotPosition->x, $y, $z + $plotPosition->z);
					$blockPositions[$position->x][$position->z][$position->y] = $this->getPlugin()->getServer()->getLevelByName($plot->levelName)->getBlock($position)->getName();

				}
			}
		}

		$date = time();

		file_put_contents("$plot->X$plot->Z" . time() . ".json.gz", gzdeflate(json_encode($blockPositions), 8, ZLIB_ENCODING_GZIP));

		return true;
	}

	public  function getUsage()
	{
		return "";
	}
}