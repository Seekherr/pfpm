<?php

declare(strict_types=1);

namespace Seeker\pfpm;

use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\world\World;
use Seeker\pfpm\pathfinding\exception\PathNotFoundException;
use Seeker\pfpm\pathfinding\pathfinder\Pathfinder;
use Seeker\pfpm\settings\PfMode;
use Seeker\pfpm\settings\PfSettings;
use Seeker\pfpm\settings\Radius;

class PfPlugin extends PluginBase {

    /**
     * Bismillah hir-rahman nir-rahim.
     */

    public function onEnable(): void {
		$to = new Vector3(29, 30, 31);
		$from = new Vector3(30, 31, 30);
		//(30, 30, 31), (30, 31, 30), (29, 31, 31), (30, 30, 30), (29, 30, 31), (29, 30, 30), (29, 31, 30)
		$radius = Radius::autoAdjust($from, $to);
		try {
			$pathfinder = (new Pathfinder($radius))->pathfind($from, $to);
			$path = $pathfinder->getPath();
		} catch (PathNotFoundException $exception) {
			var_dump("Exception caught; " . $exception->getMessage());
		}
	}
}