<?php

declare(strict_types=1);

namespace Seeker\pfpm;

use PHPUnit\Framework\TestCase;
use pocketmine\math\Vector3;
use Seeker\pfpm\pathfinding\exception\PathNotFoundException;
use Seeker\pfpm\pathfinding\pathfinder\Pathfinder;
use Seeker\pfpm\settings\PfMode;
use Seeker\pfpm\settings\PfSettings;
use Seeker\pfpm\settings\Radius;

class PfTest extends TestCase {

    /**
     * @param Radius $radius
     * @return void
     * @dataProvider \Seeker\pfpm\RadiusTest::getRadius()
     */
    public function testPathfinding(Radius $radius): void {
        try {
			$to = new Vector3(15, 16, 15);
			$from = new Vector3(30, 31, 30);
			$pathfinder = (new Pathfinder($radius))->pathfind($from, $to);
            $path = $pathfinder->getPath();
			while (($current = $path->getPathAsGenerator()->current()) !== null) {
			}
        } catch (PathNotFoundException $exception) {
            $this->fail("Exception caught; " . $exception->getMessage());
        }
    }
}