<?php

declare(strict_types=1);

namespace Seeker\pfpm\pathfinding\node;

use pocketmine\math\Vector3;
use pocketmine\world\World;
use Seeker\pfpm\pathfinding\score\ScoreCalculator;

class NodeCreator {

	public static function getAsNode(Vector3 $coordinate): PfNode {
		[$x, $y, $z] = [$coordinate->getFloorX(), $coordinate->getFloorY(), $coordinate->getFloorZ()];
		$hash = World::blockHash($x, $y, $z);
		return new PfNode($hash, $x, $y, $z, 0.1);
	}

	public static function getAsWorldNode(Vector3 $coordinate, int $mode, World $world): PfNode {
		[$x, $y, $z] = [$coordinate->getFloorX(), $coordinate->getFloorY(), $coordinate->getFloorZ()];
		$hash = World::blockHash($x, $y, $z);
		$block = $world->getBlockAt($x, $y, $z);
		$score = ScoreCalculator::calculateBlockScore($block, $mode);
		return new PfNode($hash, $x, $y, $z, $score);
	}
}