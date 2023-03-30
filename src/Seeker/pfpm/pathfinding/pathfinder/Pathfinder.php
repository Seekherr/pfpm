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
     * @var array<int, int>
     */
    private array $hashStorage = [];

    /**
     * @throws PathNotFoundException
     */
    public function pathfind(Vector3 $from, Vector3 $to): PfJob {
        $time = microtime(true);

        try {
            $fromNode = $this->resolveNode($from);
            $targetNode = $this->resolveNode($to);
            unset($this->hashStorage[$targetNode->getHash()]); // hack ;3
            $this->resolveNodeNeighbours($fromNode);
            unset($this->hashStorage);

			$stringer = new StringNodeHandler();
			$_ = $stringer->getCoordsRecursively($fromNode);
			$stringer->dumpBufferToFile("/home/seeker/Documents/PMMP/buffer_logs/test.txt");
        } catch (OutOfRadiusException $exception) {
            throw new PathNotFoundException($exception->getMessage());
        }

		return new PfJob($fromNode, $targetNode);
    }

    /**
     * @throws OutOfRadiusException
     */
    private function resolveNode(Vector3 $nodeVec): PfNode {
        if (!$this->radius->isWithin($nodeVec)) throw new OutOfRadiusException();
        $hash = World::blockHash($nodeVec->getFloorX(), $nodeVec->getFloorY(), $nodeVec->getFloorZ());
        $node = new PfNode($hash, $nodeVec, 0.1);
        if (!isset($this->hashStorage[$hash])) {
            $this->hashStorage[$node->getHash()] = 0;
        }
        return $node; // Un-traversable nodes will still be returned, to allow their neighbours to be checked.
    }

    /**
     * @throws OutOfRadiusException
     */
    private function resolveNodeNeighbours(PfNode $node): void {
        $neighbours = [];
        foreach($node->getCoordinates()->sides() as $neighbourVector) {
            if ($this->radius->isWithin($neighbourVector)) {
                if (isset($this->hashStorage[World::blockHash($neighbourVector->getFloorX(), $neighbourVector->getFloorY(), $neighbourVector->getFloorZ())])) continue;
                $neighbourNode = $this->resolveNode($neighbourVector);
                $neighbours[] = $neighbourNode;
            }
        }

        foreach ($neighbours as $neighbour) {
            $this->resolveNodeNeighbours($neighbour);
            $node->addNeighbour($neighbour);
        }
    }
}