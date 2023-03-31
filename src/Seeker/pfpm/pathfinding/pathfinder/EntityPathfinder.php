<?php

declare(strict_types=1);

namespace Seeker\pfpm\pathfinding\pathfinder;

use pocketmine\entity\Living;
use pocketmine\math\Vector3;
use Seeker\pfpm\pathfinding\exception\PathNotFoundException;
use Seeker\pfpm\pathfinding\node\PfNode;
use Seeker\pfpm\pathfinding\pathfinder\utils\VectorUtils;
use Seeker\pfpm\settings\PfSettings;
use Seeker\pfpm\settings\Radius;

class EntityPathfinder extends WorldPathfinder {

	public function __construct(
		PfSettings $settings,
		private Living $entity
	){
		parent::__construct($this->entity->getWorld(), $settings);
		$this->radius = $settings->getRadius();
	}

	private Radius $radius;

	private ?Vector3 $target = null;

	public function entityPathfind(Vector3 $target): ?PfNode {
		$entityPos = $this->entity->getPosition()->asVector3();
		if ($this->target === null || !VectorUtils::equalsFloor($this->target, $target)) {
			$this->target = $target;
			if (!$this->radius->isWithin($target)) {
				$this->radius = Radius::autoAdjust($entityPos, $target);

				$this->radius->setReserveSpace($this->getReserveRadiusSpace());
				$this->settings->setRadius($this->radius);
			}
		}

		if ($this->target === null) {
			return null;
		}

		return $this->getFirstBestNode($entityPos, $this->target);
	}

	/**
	 * So we don't have to keep reinitializing the radius (expensive)
	 * @return int[]
	 */
	public function getReserveRadiusSpace(): array {
		return [3, 1, 3];
	}
}