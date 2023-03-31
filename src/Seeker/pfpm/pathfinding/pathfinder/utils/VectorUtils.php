<?php

declare(strict_types=1);

namespace Seeker\pfpm\pathfinding\pathfinder\utils;

use pocketmine\math\Vector3;

class VectorUtils {

	public static function equalsFloor(Vector3 $x, Vector3 $y): bool {
		return (
			$x->getFloorX() === $y->getFloorX() &&
			$x->getFloorY() === $y->getFloorY() &&
			$x->getFloorZ() === $y->getFloorZ()
		);
	}
}