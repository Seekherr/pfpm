<?php

declare(strict_types=1);

namespace Seeker\pfpm\pathfinding\pathfinder;

use pocketmine\math\Vector3;
use pocketmine\world\World;
use Seeker\pfpm\pathfinding\exception\OutOfRadiusException;
use Seeker\pfpm\pathfinding\exception\PathNotFoundException;
use Seeker\pfpm\pathfinding\node\PfNode;
use Seeker\pfpm\pathfinding\node\StringNodeHandler;
use Seeker\pfpm\pathfinding\PfJob;
use Seeker\pfpm\pathfinding\score\ScoreCalculator;
use Seeker\pfpm\settings\PfSettings;
use Seeker\pfpm\settings\Radius;

class Pathfinder implements IPathfinder {

    public function __construct(
        private Radius $radius
    ){}

    /**
     * @var array<int, <PfNode, PfNode[]>>
     */
    private array $nodeStorage = [];

    /**
     * @throws PathNotFoundException
     */

    public function pathfind(Vector3 $from, Vector3 $to): PfJob {
        try {
			$fromNode = $this->resolveNode($from);
			$targetHash = World::blockHash($to->getFloorX(), $to->getFloorY(), $to->getFloorZ());
			$this->resolveNodeNeighbours($fromNode);
        } catch (OutOfRadiusException $exception) {
            throw new PathNotFoundException($exception->getMessage());
        }

		return new PfJob($fromNode, $this->nodeStorage[$targetHash][0], $this->nodeStorage);
    }

    /**
     * @throws OutOfRadiusException
     */
    private function resolveNode(Vector3 $nodeVec): PfNode {
		var_dump($this->nodeStorage);
        if (!$this->radius->isWithin($nodeVec)) throw new OutOfRadiusException();
        $hash = World::blockHash($nodeVec->getFloorX(), $nodeVec->getFloorY(), $nodeVec->getFloorZ());
        $node = new PfNode($hash, $nodeVec, 0.1);
        if (!isset($this->nodeStorage[$hash])) {
            $this->nodeStorage[$node->getHash()] = [$node, []];
        }
        return $node; // Un-traversable nodes will still be returned, to allow their neighbours to be checked.
    }

    /**
     * @throws OutOfRadiusException
     */
    private function resolveNodeNeighbours(PfNode $node): void {
        foreach($node->getCoordinates()->sides() as $neighbourVector) {
            if ($this->radius->isWithin($neighbourVector)) {
				if (isset($this->nodeStorage[World::blockHash($neighbourVector->getFloorX(), $neighbourVector->getFloorY(), $neighbourVector->getFloorZ())])) continue;
                $neighbourNode = $this->resolveNode($neighbourVector);
				$this->nodeStorage[$node->getHash()][1][] = $neighbourNode->getHash();
            }
        }

		foreach ($this->nodeStorage[$node->getHash()][1] as $neighbour) {
			if (!isset($this->nodeStorage[$neighbour])) continue;
			$this->resolveNodeNeighbours($this->nodeStorage[$neighbour][0]);
		}
    }
}