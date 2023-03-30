<?php

declare(strict_types=1);

namespace Seeker\pfpm\pathfinding\node;

class StringNodeHandler {

	/**
	 * ONLY used for recursive/buffer methods utilizing functions.
	 * @var string
	 */
	private string $buffer = "";

	/**
	 * Why not use __toString() you may be wondering? ig cuz i wanna group (string)node methods here
	 * @param PfNode $node
	 * @param bool $inline
	 * @return string
	 */
	public function getNode(PfNode $node, bool $inline = false): string {
		$this->reset();
		/*if ($inline) {
			$this->appendBuffer("Node(Hash=" . $node->getHash() . ", " .
				"Coords=" . $this->getCoords($node) . ", Parent=" . $this->getNode($node) .
			", " . "Neighbours=" . $this->getNeighbours($node));
		} else {
			$this->appendBuffer("Node(Hash=" . $node->getHash() . " => (");
			$this->nesting_level++;
			$this->appendBuffer("Coords=" . $this->getCoords($node) . ",");
			$this->appendBuffer("Parent=" . $this->getParent($node));
			var_dump($this->getParent($node));
			$this->appendBuffer("Neighbours=" . $this->getNeighbours($node));
			$this->closingBuffer(")");
		}*/ // Buggy code
		return $this->buffer;
	}

	public function getCoords(PfNode $node): string {
		$vec = $node->getCoordinates();
		return "(" . $vec->getFloorX() . ", " . $vec->getFloorY() . ", " . $vec->getFloorZ() . ")";
	}

	public function getParent(PfNode $node): string {
		$parent = $node->getParentNode() === null ? "Null" : $this->getCoords($node->getParentNode());
		return $parent;
	}

	public function getNeighbours(PfNode $node): string {
		$this->reset();
		if (count($node->getNeighbours()) === 0) {
			$this->appendBuffer("None");
		} else {
			foreach ($node->getNeighbours() as $neighbour) {
				$this->appendBuffer($this->getCoords($neighbour));
			}
		}
		return $this->buffer;
	}

	private int $nesting_level = 0;

	private ?PfNode $hackNode = null;

	/** @var array<int, int> */
	private array $nodes_nest = [];

	public function getCoordsRecursively(PfNode $node): string {
		if ($this->hackNode === null) {
			$this->reset();
			$this->hackNode = $node;
		}

		$this->appendBuffer($this->getCoords($node), $this->nodes_nest[$node->getHash()] ?? -1);
		//var_dump($this->nodes_nest);
		if (count($node->getNeighbours()) > 0) {
			$this->appendBuffer(" => [");
			$this->nesting_level++;
			$neighbours = [];
			foreach ($node->getNeighbours() as $neighbour) {
				$this->nodes_nest[$neighbour->getHash()] = $this->nesting_level;
				$neighbours[] = $neighbour;
			}
			foreach ($neighbours as $neighbour) {
				$this->getCoordsRecursively($neighbour);
			}
		}

		if ($this->hackNode === $node) {
			$this->hackNode = null;
			$this->closingBuffer("]");
		}
		return $this->buffer;
	}

	private function appendBuffer(string $input, int $nesting_level = -1): void {
		if ($nesting_level === -1) {
			$this->appendNestingLevel($this->nesting_level);
		} else {
			$this->appendNestingLevel($nesting_level);
		}
		$this->buffer .= $input;
	}

	private function closingBuffer(string $bracket): void {
		for ($i = $this->nesting_level; $i > 0; --$i) {
			$this->appendBuffer($bracket, $i);
		}
	}

	private function appendNestingLevel(int $level): void {
		$this->buffer .= "\n";
		for ($i = 0; $i < $level; $i++) {
			$this->buffer .= "    ";
		}
	}

	private function reset(): void {
		$this->buffer = "";
		$this->nesting_level = 0;
	}

	public function dumpBufferToFile(string $file): void {
		file_put_contents($file, $this->buffer);
	}
}