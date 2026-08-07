<?php

namespace NexCargo\Models;

class Address
{
  private ?string $name = null;
  private ?string $street = null;
  private ?string $houseNumber = null;
  private ?string $zipcode = null;
  private ?string $city = null;
  private ?string $country = null;

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
      'name' => $this->name,
      'street' => $this->street,
      'house_number' => $this->houseNumber,
      'zipcode' => $this->zipcode,
      'city' => $this->city,
      'country' => $this->country,
    ], static fn ($value) => $value !== null);
  }
}
