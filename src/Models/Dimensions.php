<?php

namespace NexCargo\Models;

class Dimensions
{
  private ?float $length = null;
  private ?float $width = null;
  private ?float $height = null;

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
      'length' => $this->length,
      'width' => $this->width,
      'height' => $this->height,
    ], static fn ($value) => $value !== null);
  }
}
