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

    public function __construct(
        // 1. Define the start node and the end node
        private PfNode $fromNode,
        private PfNode $targetNode
    ){}

    /**
     * @return PfPath
     * @throws PathNotFoundException
     */
    public function getPath(): PfPath {
        $toEval = new ReversePriorityQueue();
        $toEval->insert($this->fromNode, ($this->fromNode->getHeuristicScore($this->fromNode->getCoordinates(), $this->targetNode->getCoordinates())));
		while (!$toEval->isEmpty()) {
            /** @var PfNode $closestNode */
            $closestNode = $toEval->extract();
            $currentNode = $this->browseNeighboursOfNode($closestNode);
			$stringer = new StringNodeHandler();
            if ($currentNode instanceof PfNode && $currentNode->getHash() === $this->targetNode->getHash()) {
				$stringer->getNode($currentNode);
                return new PfPath($currentNode);
            }
        }
        throw new PathNotFoundException("Failed during PfJob.");
    }


    public function browseNeighboursOfNode(PfNode $node): ?PfNode {
        if ($node->getHash() === $this->targetNode->getHash()) {
            return $node;
        }

        $neighbours = [];
        foreach ($node->getNeighbours() as $neighbourNode) {
/*            $neighbourScore = $node->getHeuristicScore(
                    $this->fromNode->getCoordinates(),
                    $this->targetNode->getCoordinates()
                );
			var_dump($node->getHeuristicScore($this->fromNode->getCoordinates(), $this->targetNode->getCoordinates()));
			var_dump($neighbourScore < $node->getHeuristicScore($this->fromNode->getCoordinates(), $this->targetNode->getCoordinates()));
            if ($neighbourScore < $node->getHeuristicScore($this->fromNode->getCoordinates(), $this->targetNode->getCoordinates())) {
                $neighbours[] = $neighbourNode;
            }*/ // TODO: A*
			$neighbours[] = $neighbourNode;
        }

		foreach($neighbours as $neighbour) {
            $neighbour->setHeuristicScore($neighbour->getHeuristicScore($this->fromNode->getCoordinates(), $this->targetNode->getCoordinates()) + $node->getCoordinates()->distance($neighbour->getCoordinates()));
            $neighbour->setParentNode($node);
            $value = $this->browseNeighboursOfNode($neighbour);
            if ($value instanceof PfNode && $value->getHash() === $this->targetNode->getHash()) {
                return $value;
            }
        }

        return null;
    }
}