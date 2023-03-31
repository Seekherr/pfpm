<?php

declare(strict_types=1);

namespace Seeker\pfpm;

use PHPUnit\Framework\TestCase;
use pocketmine\math\Vector3;
use Seeker\pfpm\pathfinding\exception\PathNotFoundException;
use Seeker\pfpm\pathfinding\node\NodeCreator;
use Seeker\pfpm\pathfinding\node\PfNode;
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
			$from = new Vector3(0, 30, 0);
			$to = new Vector3(15, 15, -15);
			$targetNode = NodeCreator::getAsNode($to);
			$path = (new Pathfinder($radius))->pathfind($from, $to);
			$lastElement = null;
			foreach ($path as $node) $lastElement = $node;
			$this->assertSame($lastElement->getHash(), $targetNode->getHash(), "Pathfinder does not return the target node.");
        } catch (PathNotFoundException $exception) {
            $this->fail("Exception caught; " . $exception->getMessage());
        }
    }
}