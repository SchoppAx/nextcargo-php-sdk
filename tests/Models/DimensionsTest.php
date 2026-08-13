<?php

namespace NexCargo\Tests\Models;

use NexCargo\Models\Dimensions;
use PHPUnit\Framework\TestCase;

class DimensionsTest extends TestCase
{
  public function testToArrayWithAllFields(): void
  {
    $dimensions = new Dimensions(['length' => 60, 'width' => 40, 'height' => 30]);

    $this->assertSame(['length' => 60.0, 'width' => 40.0, 'height' => 30.0], $dimensions->toArray());
  }

  public function testToArrayFiltersNullFields(): void
  {
    $dimensions = new Dimensions(['length' => 60]);

    $result = $dimensions->toArray();

    $this->assertSame(60.0, $result['length']);
    $this->assertArrayNotHasKey('width', $result);
    $this->assertArrayNotHasKey('height', $result);
  }

  public function testToArrayWithNoFieldsIsEmpty(): void
  {
    $this->assertSame([], (new Dimensions())->toArray());
  }
}
