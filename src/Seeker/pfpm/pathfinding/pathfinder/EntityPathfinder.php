<?php

declare(strict_types=1);

namespace Seeker\pfpm\pathfinding\pathfinder;

use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\world\Position;
use pocketmine\world\World;
use Seeker\pfpm\pathfinding\node\PfNode;
use Seeker\pfpm\pathfinding\pathfinder\utils\VectorUtils;
use Seeker\pfpm\pathfinding\settings\PfSettings;
use Seeker\pfpm\pathfinding\settings\Radius;

/**
 * TODO: Make it dependency inject-able with entities.
 */
class EntityPathfinder extends WorldPathfinder {

	public function __construct(
		PfSettings $settings,
		World $world
	){
		parent::__construct($world, $settings);
		$this->radius = $settings->getRadius();
	}

	private Radius $radius;

	private ?Vector3 $target = null;

	public function entityPathfind(Position $from, Vector3 $target): ?PfNode {
		$entityPos = $from->asVector3();
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