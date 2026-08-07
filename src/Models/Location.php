<?php

namespace NexCargo\Models;

class Location
{
  private ?string $country = null;
  private ?string $postalCode = null;

  /**
   * @param array<string, mixed> $data
   */
  public function __construct(array $data = [])
  {
    foreach ($data as $key => $value) {
      $property = lcfirst(str_replace('_', '', ucwords($key, '_')));
      if (property_exists($this, $property)) {
        $this->$property = $value;
      }
    }
  }

  /**
   * @return array<string, mixed>
   */
  public function toArray(): array
  {
    return array_filter([
      'country' => $this->country,
      'postal_code' => $this->postalCode,
    ], static fn ($value) => $value !== null);
  }
}
