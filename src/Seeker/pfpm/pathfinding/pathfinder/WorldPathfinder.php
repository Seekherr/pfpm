<?php

declare(strict_types=1);

namespace Seeker\pfpm\pathfinding\pathfinder;

use pocketmine\math\Vector3;
use pocketmine\utils\ReversePriorityQueue;
use pocketmine\world\World;
use Seeker\pfpm\pathfinding\exception\OutOfRadiusException;
use Seeker\pfpm\pathfinding\exception\PathNotFoundException;
use Seeker\pfpm\pathfinding\node\NodeCreator;
use Seeker\pfpm\pathfinding\node\PfNode;
use Seeker\pfpm\pathfinding\score\ScoreCalculator;
use Seeker\pfpm\settings\PfSettings;

class WorldPathfinder implements IPathfinder {

    public function __construct(
        private World $world,
        private PfSettings $settings
    ){}

	/**
	 * @throws PathNotFoundException
	 */
	public function pathfind(Vector3 $from, Vector3 $to): array {
		$toEval = new ReversePriorityQueue();
		/** @var array<int, PfNode> $path */
		$path = [];

		$world = $this->getWorld();
		$mode = $this->getSettings()->getMode();
		$radius = $this->getSettings()->getRadius();

		$fromNode = NodeCreator::getAsWorldNode($from, $mode, $world);
		$toEval->insert($fromNode, $fromNode->getHeuristicScore($from, $to));

		$targetNode = NodeCreator::getAsWorldNode($to, $mode, $world);

		while (!$toEval->isEmpty()) {
			/** @var PfNode $closestNode */
			$closestNode = $toEval->extract();

			if ($closestNode->getHeuristicScore($from, $to) !== ScoreCalculator::DISALLOWED) {
				$path[$closestNode->getHash()] = $closestNode;
			}

			if ($closestNode->equals($targetNode)) {
				$path[$targetNode->getHash()] = $targetNode;
				return $path;
			}

			$vector = new Vector3($closestNode->getX(), $closestNode->getY(), $closestNode->getZ());
			foreach ($vector->sides() as $neighbour) {
				if (!$radius->isWithin($neighbour)) continue;
				$neighbourNode = NodeCreator::getAsWorldNode($neighbour, $mode, $world);
				if (isset($path[$neighbourNode->getHash()])) continue;
				$neighbourScore = $neighbourNode->getHeuristicScore($from, $to) + $closestNode->distance($neighbourNode);
				$neighbourNode->setHeuristicScore($neighbourScore);
				$toEval->insert($neighbourNode, $neighbourScore);
			}
		}
		throw new PathNotFoundException();
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