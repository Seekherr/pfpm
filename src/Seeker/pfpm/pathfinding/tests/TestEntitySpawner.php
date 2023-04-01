<?php

declare(strict_types=1);

namespace Seeker\pfpm\pathfinding\tests;

use Exception;
use JsonException;
use pocketmine\entity\Living;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\math\Vector3;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use Seeker\pfpm\pathfinding\pathfinder\EntityPathfinder;
use Seeker\pfpm\pathfinding\settings\PfMode;
use Seeker\pfpm\pathfinding\settings\PfSettings;
use Seeker\pfpm\pathfinding\settings\Radius;

class TestEntitySpawner {

	/**
	 * @throws Exception
	 */
	public static function spawnEntity(PluginBase $plugin, Location $spawn_location): void {
		$skin = self::getEntitySkin($plugin);
		$entity = self::initializeEntity(
			$spawn_location, $skin, 20, 5, 2
		);

		$pathfinder = new EntityPathfinder(
			new PfSettings(
				Radius::autoAdjust(new Vector3(0, 0, 1), new Vector3(1, 0, 0)),
				PfMode::WALK_ONLY
			), $entity->getWorld()
		);
		$entity->setPathfinder($pathfinder);
		if ($spawn_location->isValid()) {
			$spawn_location->getWorld()->orderChunkPopulation($spawn_location->getFloorX() >> 4, $spawn_location->getFloorZ() >> 4, null)
				->onCompletion(
					fn() => self::spawnToAllEntity($entity),
					fn() => throw new Exception("Invalid Entity location.")
				);
		}
	}

	private static function spawnToAllEntity(TestEntity $entity): void {
		$entity->spawnToAll();
		Server::getInstance()->getLogger()->debug("INITIALIZED TEST ENTITY AT {" . $entity->getLocation()->asVector3() . "}");
	}

	/**
	 * @throws Exception
	 */
	private static function initializeEntity(Location $spawn_location, Skin $skin, int $max_hp, int $scale, int $movementSpeed): TestEntity {
		$entity = new TestEntity($spawn_location, $skin);
		$entity->setSkin($skin);
		$entity->setNameTag("TestBoss");
		$entity->setMaxHealth($max_hp);
		$entity->setHealth($entity->getMaxHealth());
		$entity->setScale($scale);
		$entity->setMovementSpeed($movementSpeed);

		return $entity;
	}

	/**
	 * @param PluginBase $plugin
	 * @return Skin
	 * @throws Exception
	 */
	private static function getEntitySkin(PluginBase $plugin): Skin {
		if (!is_file($plugin->getDataFolder() . "skin.png") || !is_file($plugin->getDataFolder() . "geometry.json")) throw new Exception("Skin/geometry files not found.");
		$skinBytes = TestEntitySpawner::parseEntitySkin($plugin->getDataFolder() . "skin.png");
		$geometryName = $plugin->getConfig()->get("geometry-name");
		if($geometryName === false) throw new Exception("Invalid debug config; no geometry-name found.");
		$geometry = file_get_contents($plugin->getDataFolder() . "geometry.json");
		return new Skin("TestEntity", $skinBytes, "", $geometryName, $geometry);
	}

	/**
	 * @param string $skin
	 * @return string|null
	 */
	private static function parseEntitySkin(string $skin): ?string{
		$img = @imagecreatefrompng($skin);
		if ($img === false) {
			return null;
		}

		$bytes = "";

		$size = @getimagesize($skin);
		if (!is_array($size)) {
			return null;
		}

		$L = (int) @getimagesize($skin)[0];
		$l = (int) @getimagesize($skin)[1];

		for ($y = 0; $y < $l; $y++) {
			for ($x = 0; $x < $L; $x++) {

				$rgba = @imagecolorat($img, $x, $y);
				$a = ((~((int)($rgba >> 24))) << 1) & 0xff;
				$r = ($rgba >> 16) & 0xff;
				$g = ($rgba >> 8) & 0xff;
				$b = $rgba & 0xff;
				$bytes .= chr($r) . chr($g) . chr($b) . chr($a);

			}

		}

		@imagedestroy($img);

		if ($bytes === "") {
			return null;
		}

		return $bytes;
	}
}