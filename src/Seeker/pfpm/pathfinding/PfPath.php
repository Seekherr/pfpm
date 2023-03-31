<?php

declare(strict_types=1);

namespace Seeker\pfpm\pathfinding;

use Generator;
use pocketmine\math\Vector3;
use Seeker\pfpm\pathfinding\node\PfNode;

class PfPath {

    public function __construct(
        private PfNode $targetNode
    ){
	}

    /**
     * @return Generator<?Vector3>
     */
    public function getPathAsGenerator(): Generator {
        $parent = $this->targetNode->getParentNode();
        if ($parent instanceof PfNode) {
            $this->targetNode = $parent;
            yield $this->targetNode->getCoordinates();
        }
        yield null;
    }
}