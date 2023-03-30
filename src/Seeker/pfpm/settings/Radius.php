<?php

namespace Seeker\pfpm\settings;

use pocketmine\math\Vector3;

class Radius {


    public function __construct(
        private Vector3 $upper,
        private Vector3 $lower
    ){}

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

    public function getHigher(): Vector3 {
        return $this->upper;
    }

    public function getLower(): Vector3 {
        return $this->lower;
    }

    /**
     * Checks if a vector is within the radius.
     * @param Vector3 $vector3
     * @return bool
     */
    public function isWithin(Vector3 $vector3): bool {
        return (
            $vector3->getFloorX() <= $this->getHigher()->getFloorX() &&
            $vector3->getFloorY() <= $this->getHigher()->getFloorY() &&
            $vector3->getFloorZ() <= $this->getHigher()->getFloorZ() &&
            $vector3->getFloorX() >= $this->getLower()->getFloorX() &&
            $vector3->getFloorY() >= $this->getLower()->getFloorY() &&
            $vector3->getFloorZ() >= $this->getLower()->getFloorZ()
        );
    }
}