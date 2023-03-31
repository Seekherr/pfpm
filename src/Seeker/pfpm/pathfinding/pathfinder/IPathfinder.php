<?php

declare(strict_types=1);

namespace Seeker\pfpm\pathfinding\pathfinder;

use pocketmine\math\Vector3;
use Seeker\pfpm\settings\PfSettings;

interface IPathfinder {
    public function pathfind(Vector3 $from, Vector3 $to): array;
}