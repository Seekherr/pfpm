<?php

declare(strict_types=1);

namespace Seeker\pfpm;

use Exception;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\world\Position;
use pocketmine\world\World;
use Seeker\pfpm\pathfinding\tests\TestEntity;
use Seeker\pfpm\pathfinding\tests\TestEntitySpawner;

class PfPlugin extends PluginBase {

    /**
     * Bismillah hir-rahman nir-rahim.
     */

	const DEBUG_MODE = "debug";
	const LIB_MODE = "lib";

    public function onEnable(): void {
		$this->saveResource('config.yml');
		$this->saveResource('skin.png');
		$this->saveResource('geometry.json');
		$mode = $this->getConfig()->get("mode");
		if ($mode === self::DEBUG_MODE) {
			try {
				$spawnLoc = $this->getConfig()->get("spawn-location");
				if ($spawnLoc === false) {
					$this->getServer()->getLogger()->debug("Spawn location for debug entity not found!");
					return;
				}
				EntityFactory::getInstance()->register(TestEntity::class, function(World $world, CompoundTag $nbt): TestEntity{
					return new TestEntity(EntityDataHelper::parseLocation($nbt, $world), TestEntity::parseSkinNBT($nbt));
				}, ["TestEntity"]);
				$loc = $this->parseSpawnLocation($spawnLoc);
				TestEntitySpawner::spawnEntity($this, $loc);
			} catch (Exception $exception) {
				$this->getServer()->getLogger()->debug("Exception encountered while testing using TestEntity. Exception: ". $exception->getMessage());
			}
		}
	}

	/**
	 * @throws Exception
	 */
	private function parseSpawnLocation(string $locationStr): Location {
		$explodedLoc = explode(":", $locationStr);
		if (count($explodedLoc) === 4) {
			[$x, $y, $z, $worldName] = $explodedLoc;
			$world = $this->getServer()->getWorldManager()->getWorldByName($worldName);
			if ($world instanceof World) {
				return new Location((float)$x, (float)$y, (float)$z, $world, 0, 0);
			}
		}
		throw new Exception("Invalid location configuration.");
	}
}