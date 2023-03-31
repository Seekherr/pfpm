<?php

namespace Seeker\pfpm\settings;

use pocketmine\math\Vector3;

class Radius {


	private int $upperX;
	private int $upperY;
	private int $upperZ;

	private int $lowerX;
	private int $lowerY;
	private int $lowerZ;

    public function __construct(
		Vector3 $upper,
		Vector3 $lower
    ){
		$this->upperX = $upper->getFloorX();
		$this->upperY = $upper->getFloorY();
		$this->upperZ = $upper->getFloorZ();
		$this->lowerX = $lower->getFloorX();
		$this->lowerY = $lower->getFloorY();
		$this->lowerZ = $lower->getFloorZ();
	}

    public static function autoAdjust(Vector3 $from, Vector3 $to): Radius {
        $upperX = max($from->getFloorX(), $to->getFloorX());
        $upperY = max($from->getFloorY(), $to->getFloorY());
        $upperZ = max($from->getFloorZ(), $to->getFloorZ());

        $lowerX = min($from->getFloorX(), $to->getFloorX());
        $lowerY = min($from->getFloorY(), $to->getFloorY());
        $lowerZ = min($from->getFloorZ(), $to->getFloorZ());

        return new Radius(
            new Vector3($upperX, $upperY, $upperZ),
            new Vector3($lowerX, $lowerY, $lowerZ)
        );
    }

	public function getHigherFloors(): array {
		return [$this->upperX, $this->upperY, $this->upperZ];
	}

	public function getLowerFloors(): array {
		return [$this->lowerX, $this->lowerY, $this->lowerZ];
	}

    /**
     * Checks if a vector is within the radius.
     * @param Vector3 $vector3
     * @return bool
     */
    public function isWithin(Vector3 $vector3): bool {
		$x = $vector3->getFloorX();
		$y = $vector3->getFloorY();
		$z = $vector3->getFloorZ();
        return (
            $x <= $this->upperX &&
            $y <= $this->upperY &&
            $z <= $this->upperZ &&
            $x >= $this->lowerX &&
            $y >= $this->lowerY &&
            $z >= $this->lowerZ
        );
    }
}