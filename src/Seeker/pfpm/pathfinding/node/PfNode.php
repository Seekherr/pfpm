<?php

namespace Seeker\pfpm\pathfinding\node;

use pocketmine\math\Vector3;

class PfNode {

    public function __construct(
		private int $hash,
        private int $x,
		private int $y,
		private int $z,
        private float $score,
    ){}

	private ?PfNode $childNode = null;

	private ?float $heuristicScore = null;

	public function getHash(): int {
		return $this->hash;
	}

	/**
	 * @return int
	 */
	public function getX(): int {
		return $this->x;
	}

	/**
	 * @return int
	 */
	public function getY(): int {
		return $this->y;
	}

	/**
	 * @return int
	 */
	public function getZ(): int {
		return $this->z;
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
		$vec = new Vector3($this->getX(), $this->getY(), $this->getZ());
        return $this->heuristicScore ??
            ($from->distance($vec) + $to->distance($vec)) + $this->getScore();
    }

    /**
     * @return float
     */
    public function getScore(): float {
        return $this->score;
    }

	public function equals(PfNode $node): bool {
		return (
			$node->getX() === $this->getX() &&
			$node->getY() === $this->getY() &&
			$node->getZ() === $this->getZ()
		);
	}


	/**
	 * pmmp ripoff xD
	 * @param PfNode $node
	 * @return float
	 */
	public function distance(PfNode $node): float {
		return sqrt((($this->getX() - $node->getX()) ** 2) + (($this->getY() - $node->getY()) ** 2) + (($this->getZ() - $node->getZ()) ** 2));
	}
}