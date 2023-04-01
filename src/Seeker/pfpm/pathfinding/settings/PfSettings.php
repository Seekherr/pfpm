<?php

declare(strict_types=1);

namespace Seeker\pfpm\pathfinding\settings;

use Seeker\pfpm\pathfinding\score\ScoreRegistery;

class PfSettings {

    public function __construct(
        private Radius $radius,
        private int $mode,
        private ?ScoreRegistery $scoreRegistery = null
    ){}

    /**
     * @return Radius
     */
    public function getRadius(): Radius {
        return $this->radius;
    }

    /**
     * @return int
     */
    public function getMode(): int {
        return $this->mode;
    }

    /**
     * @return null|ScoreRegistery
     */
    public function getScoreRegistery(): ?ScoreRegistery {
        return $this->scoreRegistery;
    }

    /**
     * @param Radius $radius
     */
    public function setRadius(Radius $radius): void {
        $this->radius = $radius;
    }

    /**
     * @param int $mode
     */
    public function setMode(int $mode): void {
        $this->mode = $mode;
    }

    /**
     * @param ScoreRegistery $scoreRegistery
     */
    public function setScoreRegistery(ScoreRegistery $scoreRegistery): void {
        $this->scoreRegistery = $scoreRegistery;
    }
}