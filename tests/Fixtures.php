<?php

namespace NexCargo\Tests;

final class Fixtures
{
  /** @return array<string, string> */
  public static function addressData(): array
  {
    return [
      'name' => 'Test GmbH',
      'street' => 'Teststraße',
      'house_number' => '1',
      'zipcode' => '50667',
      'city' => 'Köln',
      'country' => 'DE',
      'phone' => '+49 123 4567890',
    ];
  }

  /** @return array<string, mixed> */
  public static function packageData(): array
  {
    return ['weight' => 10.0, 'length' => 50, 'width' => 30, 'height' => 20];
  }

  /** @return array<string, mixed> */
  public static function shipmentData(): array
  {
    return [
      'carrier' => 'auto',
      'pickup_date' => '2026-01-15',
      'pickup_time_from' => '09:00',
      'pickup_time_to' => '12:00',
      'delivery_date' => '2026-01-16',
      'delivery_time_from' => '08:00',
      'delivery_time_to' => '18:00',
      'pickup' => self::addressData(),
      'delivery' => self::addressData(),
      'packages' => [self::packageData()],
    ];
  }

  /** @return array<string, mixed> */
  public static function quoteData(): array
  {
    $data = self::shipmentData();
    unset($data['carrier']);
    return $data;
  }
}
