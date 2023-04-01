<?php

declare(strict_types=1);

namespace Seeker\pfpm\pathfinding\pathfinder;

use pocketmine\math\Vector3;
use pocketmine\utils\ReversePriorityQueue;
use Seeker\pfpm\pathfinding\exception\PathNotFoundException;
use Seeker\pfpm\pathfinding\node\NodeCreator;
use Seeker\pfpm\pathfinding\node\PfNode;
use Seeker\pfpm\pathfinding\score\ScoreCalculator;
use Seeker\pfpm\pathfinding\settings\Radius;

class Pathfinder implements IPathfinder {

    public function __construct(
		protected Radius $radius
    ){}


	/**
	 * @param Vector3 $from
	 * @param Vector3 $to
	 * @return array<int, PfNode>
	 * @throws PathNotFoundException
	 */
	public function pathfind(Vector3 $from, Vector3 $to): array {
		$toEval = new ReversePriorityQueue();
		/** @var array<int, PfNode> $path */
		$path = [];
		$fromNode = NodeCreator::getAsNode($from);
		$toEval->insert($fromNode, $fromNode->getHeuristicScore($from, $to));

		$targetNode = NodeCreator::getAsNode($to);

		while (!$toEval->isEmpty()) {
			/** @var PfNode $closestNode */
			$closestNode = $toEval->extract();
			// we still want to check its neighbours
			if ($closestNode->getHeuristicScore($from, $to) !== ScoreCalculator::DISALLOWED) {
				$path[$closestNode->getHash()] = $closestNode;
			}
			if ($closestNode->equals($targetNode)) {
				$path[$targetNode->getHash()] = $targetNode;
				return $path;
			}

			$asVec = new Vector3($closestNode->getX(), $closestNode->getY(), $closestNode->getZ());
			foreach ($asVec->sides() as $neighbour) {
				if (!$this->radius->isWithin($neighbour)) continue;
				$neighbourNode = NodeCreator::getAsNode($neighbour);
				if (isset($path[$neighbourNode->getHash()])) continue;
				$neighbourScore = $neighbourNode->getHeuristicScore($from, $to) + $closestNode->distance($neighbourNode);
				$neighbourNode->setHeuristicScore($neighbourScore);
				$toEval->insert($neighbourNode, $neighbourScore);
			}
		}
		throw new PathNotFoundException();
    }

	public function getFirstBestNode(Vector3 $from, Vector3 $to): ?PfNode {
		$fromNode = NodeCreator::getAsNode($from);
		$bestScore = 10000000;
		$bestNode = null;
		foreach ($from->sides() as $neighbour) {
			if (!$this->radius->isWithin($neighbour)) continue;
			$score = $neighbour->getHeuristicScore($from, $to) + $fromNode->distance($fromNode);
			if($score > 0 && $score < $bestScore) {
				$bestScore = $score;
				$bestNode = NodeCreator::getAsNode($neighbour);
			}
		}
		return $bestNode;
	}
}