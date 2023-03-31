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
			new Vector3(0, 30, 0),
            new Vector3(15, 15, -15)
        ];

        foreach ($randomValues as $vector3) {
            $this->assertTrue($radius->isWithin($vector3));
        }
    }

    /**
     * @return Radius[]
     * @phpstan-return Radius[]
     */
    public function getRadius(): array {
		$from = new Vector3(0, 30, 0);
		$to = new Vector3(15, 15, -15);
        $radius = Radius::autoAdjust($from, $to);

        return [
            [$radius]
        ];
    }
}