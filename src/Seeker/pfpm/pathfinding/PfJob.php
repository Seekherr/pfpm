<?php

namespace Seeker\pfpm\pathfinding;

use pocketmine\utils\ReversePriorityQueue;
use Seeker\pfpm\pathfinding\exception\PathNotFoundException;
use Seeker\pfpm\pathfinding\node\PfNode;
use Seeker\pfpm\pathfinding\node\StringNodeHandler;

class PfJob {

    /**
     * 1. Create an open list and a closed list of nodes.
    6. If the lowest-scoring node is the target node, we have found the path. Return the path from start to goal.
    7. Generate the successors of the current node (i.e. the nodes that can be reached from the current node).
    8. For each successor, calculate its g-score as the g-score of the current node plus the cost of reaching the successor, and its h-score as the estimated distance from the successor to the goal node.
    9. If the successor is already on the open list, and the new g-score is higher than the existing g-score for the node, skip this successor and continue with the next one.
     */

	/**
	 * @param PfNode $fromNode
	 * @param PfNode $targetNode
	 * @param array<int, <PfNode, PfNode[]>> $nodeStorage
	 */
    public function __construct(
        private PfNode $fromNode,
        private PfNode $targetNode,
		private array $nodeStorage
    ){}

    /**
     * @return PfPath
     * @throws PathNotFoundException
     */
    public function getPath(): PfPath {
		$time = microtime(true);
        $toEval = new ReversePriorityQueue();
        $toEval->insert($this->fromNode, ($this->fromNode->getHeuristicScore($this->fromNode->getCoordinates(), $this->targetNode->getCoordinates())));
		while (!$toEval->isEmpty()) {
            /** @var PfNode $closestNode */
            $closestNode = $toEval->extract();
			if ($closestNode->getHash() === $this->targetNode->getHash()) {
				var_dump("Time taken:" . microtime(true) - $time);
				return new PfPath($closestNode);
			}
			foreach ($this->nodeStorage[$closestNode->getHash()][1] as $neighbour) {
				$node = $this->nodeStorage[$neighbour][0];
				if (isset($node) && $node instanceof PfNode) {
					$node->setParentNode($closestNode);
					$node->setHeuristicScore($node->getHeuristicScore($this->fromNode->getCoordinates(), $this->targetNode->getCoordinates()) + $closestNode->getCoordinates()->distance($node->getCoordinates()));
					$toEval->insert($node, $node->getHeuristicScore($this->fromNode->getCoordinates(), $this->targetNode->getCoordinates()));
				}
			}
        }
		throw new PathNotFoundException("Failed during PfJob.");
	}
}