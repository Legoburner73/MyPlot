<?php
/**
 * Created by PhpStorm.
 * User: funtimes
 * Date: 8/20/17
 * Time: 3:02 AM
 */

namespace MyPlot\task;


use MyPlot\MyPlot;
use MyPlot\Plot;
use pocketmine\Player;
use pocketmine\scheduler\PluginTask;
use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat;

class SharePlotTask extends PluginTask {

	/** @var MyPlot */
	public $plugin;

	/** @var Plot */
	public $plot;

	/** @var Player */
	public $player;

	public $args;
	private $x = 0;
	private $z = 0;

	public $blockPositions = [];
	public $plotSize;
	public $plotPosition;

	public function __construct(MyPlot $owner, Player $player, Plot $plot, $args) {
		$this->plugin = $owner;
		$this->player = $player;
		$this->plot = $plot;
		$this->args = $args;

		$this->plotSize = $this->plugin->getLevelSettings($this->plot->levelName)->plotSize;

		$this->plotPosition = $this->plugin->getPlotPosition($this->plot);

		parent::__construct($owner);
	}

	public function onRun(int $currentTick) {

		$height = 256; // hard-coding this should be fine, for now, and for performance

		for ($x = $this->x; $x < $this->plotSize; $x++) {
			$this->x = $x;
			for ($z = $this->z; $z < $this->plotSize; $z++) {
				for ($y = 0; $y < $height; $y++) {

					$position = new Vector3($x + $this->plotPosition->x, $y, $z + $this->plotPosition->z);
					$this->blockPositions[$x][$z][$y] = $this->plugin->getServer()->getLevelByName($this->plot->levelName)->getBlock($position)->getId();

				}
				$this->z++;
				return;
			}
			$this->z = 0;
		}

		$plotsSaveDir = mb_strtolower("plots/" . $this->player->getName());

		if (is_dir($plotsSaveDir) || mkdir($plotsSaveDir, 0700, true)) {
			file_put_contents("$plotsSaveDir/" . (count($this->args) === 1 ? $this->args[0] : $this->plot->X . "." . $this->plot->Z . "." . time()) . ".json.gz", gzdeflate(json_encode($this->blockPositions), 8, ZLIB_ENCODING_GZIP));
			$this->player->sendMessage( TextFormat::GREEN . "Plot shared successfully!");
		} else {
			$this->player->sendMessage( TextFormat::RED . "Could not share plot. Contact an admin.");
			$this->plugin->getServer()->getLogger()->error("Could not create plots directory for user " . $this->player->getName());
		}

		$this->getHandler()->cancel();
	}
}