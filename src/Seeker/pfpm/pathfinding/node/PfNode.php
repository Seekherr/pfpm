<?php

namespace Seeker\pfpm\pathfinding\node;

use pocketmine\math\Vector3;

class PfNode {

    public function __construct(
        private int $hash,
        private Vector3 $coordinates,
        private float $score,
    ){}

	private ?PfNode $parentNode = null;

	private ?float $heuristicScore = null;

    /**
     * @return int
     */
    public function getHash(): int {
        return $this->hash;
    }

    /**
     * @return Vector3
     */
    public function getCoordinates(): Vector3 {
        return $this->coordinates;
    }

    public function setHeuristicScore(float $heuristicScore): void {
        $this->heuristicScore = $heuristicScore;
    }

    /**
     * f = g ($from node to current) + h (current node to $to). Also add the block hardness score
     * @param Vector3 $from
     * @param Vector3 $to
     * @return float
     */
    public function getHeuristicScore(Vector3 $from, Vector3 $to): float {
        return $this->heuristicScore ??
            ($from->distance($this->getCoordinates()) + $to->distance($this->getCoordinates())) + $this->getScore();
    }

    /**
     * @return float
     */
    public function getScore(): float {
        return $this->score;
    }

    /**
     * @return null|PfNode
     */
    public function getParentNode(): ?PfNode {
        return $this->parentNode;
    }

    /**
     * @param PfNode $parentNode
     */
    public function setParentNode(PfNode $parentNode): void {
        $this->parentNode = $parentNode;
    }
}