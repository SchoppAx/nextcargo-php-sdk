<?php

namespace NexCargo\Models;

class Recipient
{
  private ?string $name = null;
  private ?Address $address = null;

  /**
   * @param array<string, mixed> $data
   */
  public function __construct(array $data = [])
  {
    foreach ($data as $key => $value) {
      $property = lcfirst(str_replace('_', '', ucwords($key, '_')));
      if ($property === 'address' && $value instanceof Address) {
        $this->address = $value;
      } elseif (property_exists($this, $property)) {
        $this->$property = $value;
      }
    }
  }

  /**
   * @return array<string, mixed>
   */
  public function toArray(): array
  {
    $data = [
      'name' => $this->name,
    ];

    if ($this->address !== null) {
      $data['address'] = $this->address->toArray();
    }

    return array_filter($data, static fn ($value) => $value !== null);
  }
}
