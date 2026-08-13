<?php

namespace NexCargo\Tests\Models;

use NexCargo\Models\Location;
use PHPUnit\Framework\TestCase;

class LocationTest extends TestCase
{
  public function testToArrayWithAllFields(): void
  {
    $location = new Location(['country' => 'DE', 'postal_code' => '50667']);

    $this->assertSame(['country' => 'DE', 'postal_code' => '50667'], $location->toArray());
  }

  public function testToArrayFiltersNullFields(): void
  {
    $location = new Location(['country' => 'DE']);

    $result = $location->toArray();

    $this->assertSame('DE', $result['country']);
    $this->assertArrayNotHasKey('postal_code', $result);
  }

  public function testToArrayWithNoFieldsIsEmpty(): void
  {
    $this->assertSame([], (new Location())->toArray());
  }
}
