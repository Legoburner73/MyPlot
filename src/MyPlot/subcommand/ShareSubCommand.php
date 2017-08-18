<?php
/**
 * Created by PhpStorm.
 * User: funtimes
 * Date: 8/18/17
 * Time: 1:40 PM
 */

namespace MyPlot\subcommand;

use pocketmine\command\CommandSender;
use pocketmine\Player;

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
		return [];
	}

	public function canUse(CommandSender $sender) {
		return ($sender instanceof Player) and $sender->hasPermission("myplot.command.reset");
	}

	public  function execute(CommandSender $sender, array $args)
	{
		$sender->getServer()->getLogger()->info($sender->getName());
	}

	public  function getUsage()
	{
		return "";
	}
}