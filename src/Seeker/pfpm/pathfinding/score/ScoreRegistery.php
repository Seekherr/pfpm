<?php

namespace Seeker\pfpm\pathfinding\score;

class ScoreRegistery {
    /**
     * @var array<string, float>
     */
    private array $scores = [];

    /**
     * Use `Block` full name as $id
     * @param string $id
     * @param float $score
     * @return $this
     */
    public function setScore(string $id, float $score): ScoreRegistery {
        $this->scores[$id] = $score;
        return $this;
    }

    public function getScore(string $id): ?float {
        return $this->scores[$id] ?? null;
    }
}