<?php

namespace NexCargo\Tests\Models;

use NexCargo\Models\Dimensions;
use NexCargo\Models\Parcel;
use PHPUnit\Framework\TestCase;

class ParcelTest extends TestCase
{
  public function testToArrayWithWeightOnly(): void
  {
    $parcel = new Parcel(['weight' => 15.5]);

    $this->assertSame(['weight' => 15.5], $parcel->toArray());
  }

  public function testToArrayWithDimensions(): void
  {
    $dimensions = new Dimensions(['length' => 60, 'width' => 40, 'height' => 30]);
    $parcel = new Parcel(['weight' => 15.5, 'dimensions' => $dimensions]);

    $result = $parcel->toArray();

    $this->assertSame(15.5, $result['weight']);
    $this->assertSame(60.0, $result['length']);
    $this->assertSame(40.0, $result['width']);
    $this->assertSame(30.0, $result['height']);
  }

  public function testToArrayWithNoFieldsIsEmpty(): void
  {
    $this->assertSame([], (new Parcel())->toArray());
  }
}
