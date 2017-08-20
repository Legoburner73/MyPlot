<?php
/**
 * Created by PhpStorm.
 * User: funtimes
 * Date: 8/20/17
 * Time: 12:12 AM
 */

namespace MyPlot\task;

use MyPlot\MyPlot;
use MyPlot\Plot;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\scheduler\PluginTask;
use pocketmine\block\Block;
use pocketmine\utils\TextFormat;

class LoadPlotTask extends PluginTask {

	/** @var Player */
	private $player;

	/** @var MyPlot */
	private $plugin;

	/** @var string */
	private $filePath;

	/** @var Plot */
	private $plot;

	/** @var Vector3 */
	private $position;

	private $loadedPlotData;

	private $plotPosition;
	private $height = 256;
	private $plotSize;

	public function __construct(MyPlot $owner, Player $player, string $filePath, Plot $plot) {
		$this->player = $player;
		$this->plugin = $owner;
		$this->filePath = $filePath;
		$this->plot = $plot;
		$this->position = new Vector3(0, 0, 0);

		$gzfile = gzopen($this->filePath, "r");
		$this->loadedPlotData = json_decode(gzread($gzfile, 65536 * 1024), true);
		gzclose($gzfile);

		$this->plotSize = $this->plugin->getLevelSettings($this->plot->levelName)->plotSize;
		$this->height = 256; // hard-coding this should be fine, for now, and for performance

		$this->plotPosition = $this->plugin->getPlotPosition($this->plot);

		if ($this->plotSize > count($this->loadedPlotData)) {
			$this->plotSize = count($this->loadedPlotData);
		}

		parent::__construct($owner);
	}

	public function onRun(int $currentTick) {

		for ($x = $this->position->x; $x < $this->plotSize; $x++) {
			$this->position->x = $x;
			for ($z = $this->position->z; $z < $this->plotSize; $z++) {

				for ($y = 0; $y < $this->height; $y++) {
					$adjusted_position = new Vector3($x + $this->plotPosition->x, $y, $z + $this->plotPosition->z);
					$this->player->getLevel()->setBlock($adjusted_position, Block::get($this->loadedPlotData[$x][$z][$y]));
				}
				$this->position->z++;
				return;
			}
			$this->position->z = 0;
		}

		$this->player->sendMessage( TextFormat::GREEN . "Loading done.");

		$this->getHandler()->cancel();
	}
}