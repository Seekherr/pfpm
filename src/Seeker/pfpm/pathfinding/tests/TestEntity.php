<?php

declare(strict_types=1);

namespace Seeker\pfpm\pathfinding\tests;

use pocketmine\entity\Human;
use pocketmine\entity\Living;
use pocketmine\math\Vector3;
use Seeker\pfpm\pathfinding\pathfinder\EntityPathfinder;

class TestEntity extends Human {
	private ?EntityPathfinder $pathfinder = null;
	private ?Vector3 $target = null;

	const TARGET_DISTANCE = 15;

	public function setPathfinder(EntityPathfinder $pathfinder): void {
		$this->pathfinder = $pathfinder;
	}

	public function onUpdate(int $currentTick): bool {
		if ($this->target instanceof Vector3) {
			if ($this->target->distance($this->getPosition()) > self::TARGET_DISTANCE) {
				$this->target = null;
				return parent::onUpdate($currentTick);
			}
			if ($this->pathfinder instanceof EntityPathfinder) {
				$path = $this->pathfinder->entityPathfind($this->getPosition(), $this->target);
				var_dump($path);
				if ($path !== null) {
					$this->lookAt($this->target);
					$this->move($path->getX(), $path->getY(), $path->getZ());
					return parent::onUpdate($currentTick);
				}
			}
		} else {
			foreach ($this->getViewers() as $viewer) {
				$pos = $viewer->getPosition();
				if ($pos->distance($this->getPosition()) <= self::TARGET_DISTANCE) {
					$this->target = $pos;
				}
			}
		}
		return parent::onUpdate($currentTick);
	}
}