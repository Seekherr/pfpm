<?php

declare(strict_types=1);

namespace Seeker\pfpm\pathfinding\pathfinder;

use pocketmine\math\Vector3;
use pocketmine\world\World;
use Seeker\pfpm\pathfinding\exception\OutOfRadiusException;
use Seeker\pfpm\pathfinding\exception\PathNotFoundException;
use Seeker\pfpm\pathfinding\node\PfNode;
use Seeker\pfpm\pathfinding\PfJob;
use Seeker\pfpm\pathfinding\score\ScoreCalculator;
use Seeker\pfpm\settings\PfSettings;

class WorldPathfinder implements IPathfinder {

    public function __construct(
        private World $world,
        private PfSettings $settings
    ){}

    /**
     * @var array<int, int>
     */
    private array $hashStorage = [];

    /**
     * @throws PathNotFoundException
     */
    public function pathfind(Vector3 $from, Vector3 $to): PfJob {
        try {
            $fromNode = $this->resolveNode($from, $this->getWorld());
            $targetNode = $this->resolveNode($to, $this->getWorld());
            unset($this->hashStorage[$targetNode->getHash()]); // hack ;3
            $this->resolveNodeNeighbours($fromNode, $this->getWorld());
            unset($this->hashStorage);
        } catch (OutOfRadiusException $exception) {
            throw new PathNotFoundException($exception->getMessage());
        }
        return new PfJob($fromNode, $targetNode);
    }

    public function resolveNode(Vector3 $nodeVec, World $world): PfNode {
        $block = $world->getBlockAt($nodeVec->getFloorX(), $nodeVec->getFloorY(), $nodeVec->getFloorZ());
        $blockHash = World::blockHash($nodeVec->getFloorX(), $nodeVec->getFloorY(), $nodeVec->getFloorZ());
        $score = ScoreCalculator::calculateBlockScore($block, $this->getSettings()->getMode(), $this->getSettings()->getScoreRegistery());
        $node = new PfNode($blockHash, $nodeVec, $score);
        if ($score !== ScoreCalculator::DISALLOWED && !isset($this->hashStorage[$blockHash])) {
            $this->hashStorage[$node->getHash()] = 0;
        }
        return $node; // Un-traversable nodes will still be returned, to allow their neighbours to be checked.
    }


    public function resolveNodeNeighbours(PfNode $node, World $world): void {
        $neighbours = [];
        foreach($node->getCoordinates()->sides() as $neighbourVector) {
            if ($this->getSettings()->getRadius()->isWithin($neighbourVector)) {
                if (isset($this->hashStorage[World::blockHash($neighbourVector->getFloorX(), $neighbourVector->getFloorY(), $neighbourVector->getFloorZ())])) continue;
                $neighbourNode = $this->resolveNode($neighbourVector, $this->getWorld());
                $neighbours[] = $neighbourNode;
            }
        }

        foreach ($neighbours as $neighbour) {
            $this->resolveNodeNeighbours($neighbour, $world);
            $node->addNeighbour($neighbour);
        }
    }

    /**
     * @return World
     */
    public function getWorld(): World {
        return $this->world;
    }

    /**
     * @return PfSettings
     */
    public function getSettings(): PfSettings {
        return $this->settings;
    }
}