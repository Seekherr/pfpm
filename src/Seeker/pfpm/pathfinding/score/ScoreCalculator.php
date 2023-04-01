<?php

namespace Seeker\pfpm\pathfinding\score;

use pocketmine\block\Air;
use pocketmine\block\Block;
use Seeker\pfpm\pathfinding\settings\PfMode;

class ScoreCalculator {
    const DISALLOWED = -100.0;

    public static function calculateBlockScore(Block $block, int $mode, ScoreRegistery $scoreRegistery = null): float {
        if ($scoreRegistery instanceof ScoreRegistery) {
            $regScore = $scoreRegistery->getScore($block->getName());
            if ($regScore !== null) {
                return $regScore;
            }
        }

        if ($mode === PfMode::WALK_ONLY) {
            if ($block instanceof Air) {
                return 0.1;
            } else {
                return ScoreCalculator::DISALLOWED;
            }
        } else if ($mode === PfMode::BREAK_BLOCKS) {
            if (!$block->getBreakInfo()->isBreakable()) {
                if ($block instanceof Air) {
                    return 0.1;
                } else {
                    return ScoreCalculator::DISALLOWED;
                }
            } else {
                return $block->getBreakInfo()->getHardness();
            }
        }
        var_dump("You done configured me wrong bruh..."); // I know this violates not doing more than mentioned but ;3
        return ScoreCalculator::DISALLOWED; //:trollface:
    }
}