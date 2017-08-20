<?php
/**
 * Created by PhpStorm.
 * User: funtimes
 * Date: 8/18/17
 * Time: 6:15 PM
 */

namespace MyPlot\SubCommand;

use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\Player;

class LoadPlotSubCommand extends SubCommand
{
	public function getName()
	{
		return "loadplot";
	}

	public function getDescription()
	{
		return "Allows you to load a plot from the online repository";
	}

	public function getAliases() {
		return ["ld"];
	}

	public function canUse(CommandSender $sender) {
		return ($sender instanceof Player) and $sender->hasPermission("myplot.command.loadplot");
	}

	public  function execute(CommandSender $sender, array $args)
	{

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

		file_put_contents("blocks.json.gz", gzdeflate(json_encode($blockPositions), 8, ZLIB_ENCODING_GZIP));

		return true;
	}

	public  function getUsage()
	{
		return "";
	}
}