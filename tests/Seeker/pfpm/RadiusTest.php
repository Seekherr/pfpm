<?php

declare(strict_types=1);

namespace Seeker\pfpm;

use PHPUnit\Framework\TestCase;
use pocketmine\math\Vector3;
use Seeker\pfpm\settings\Radius;

class RadiusTest extends TestCase {


    /**
     * @param Radius $radius
     * @return void
     * @dataProvider getRadius
     */
    public function testRadius(Radius $radius): void {
        $randomValues = [
            new Vector3(-30, 25, 34),
            new Vector3(-29, 23, 39),
            new Vector3(-30, 30, 30),
            new Vector3(-30, 20, 40),
            new Vector3(-30, 30, 40)
        ];

		$randomValues = [];
        foreach ($randomValues as $vector3) {
            $this->assertTrue($radius->isWithin($vector3));
        }
    }

    /**
     * @return Radius[]
     * @phpstan-return Radius[]
     */
    public function getRadius(): array {
		$to = new Vector3(15, 16, 15);
		$from = new Vector3(30, 31, 30);
        $radius = Radius::autoAdjust($from, $to);

        return [
            [$radius]
        ];
    }
}