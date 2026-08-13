<?php

namespace NexCargo\Tests\Models;

use NexCargo\Models\ShipmentRequest;
use NexCargo\Models\Address;
use NexCargo\Tests\Fixtures;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ShipmentRequestTest extends TestCase
{
  /** @return array<string, array{0: string}> */
  public static function requiredFieldsProvider(): array
  {
    return [
      'carrier' => ['carrier'],
      'pickup_date' => ['pickup_date'],
      'pickup_time_from' => ['pickup_time_from'],
      'pickup_time_to' => ['pickup_time_to'],
      'delivery_date' => ['delivery_date'],
      'delivery_time_from' => ['delivery_time_from'],
      'delivery_time_to' => ['delivery_time_to'],
      'pickup' => ['pickup'],
      'delivery' => ['delivery'],
      'packages' => ['packages'],
    ];
  }

  #[DataProvider('requiredFieldsProvider')]
  public function testThrowsWhenRequiredFieldIsMissing(string $field): void
  {
    $this->expectException(\InvalidArgumentException::class);
    $this->expectExceptionMessage('ShipmentRequest requires ' . $field . '.');

    $data = Fixtures::shipmentData();
    unset($data[$field]);
    new ShipmentRequest($data);
  }

  public function testToArrayReturnsNormalizedData(): void
  {
    $request = new ShipmentRequest(Fixtures::shipmentData());
    $result = $request->toArray();

    $this->assertSame('auto', $result['carrier']);
    $this->assertSame('2026-01-15', $result['pickup_date']);
    $this->assertIsArray($result['pickup']);
    $this->assertIsArray($result['packages']);
  }

  public function testNormalizesNestedSelfInstance(): void
  {
    $inner = new ShipmentRequest(Fixtures::shipmentData());
    $data = Fixtures::shipmentData();
    $data['related'] = $inner;

    $result = (new ShipmentRequest($data))->toArray();

    $this->assertIsArray($result['related']);
    $this->assertSame('auto', $result['related']['carrier']);
  }

  public function testNormalizesArrayObject(): void
  {
    $data = Fixtures::shipmentData();
    $data['metadata'] = new \ArrayObject(['source' => 'test']);

    $result = (new ShipmentRequest($data))->toArray();

    $this->assertSame(['source' => 'test'], $result['metadata']);
  }

  public function testNormalizesGenericObjectWithToArrayMethod(): void
  {
    $data = Fixtures::shipmentData();
    $data['pickup'] = new Address(Fixtures::addressData());

    $result = (new ShipmentRequest($data))->toArray();

    $this->assertSame(Fixtures::addressData(), $result['pickup']);
  }
}
