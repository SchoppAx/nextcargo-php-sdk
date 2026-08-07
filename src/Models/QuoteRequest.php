<?php

namespace NexCargo\Models;

class QuoteRequest
{
  /** @var array<string, mixed> */
  private array $data;

  /**
   * @param array<string, mixed> $data
   */
  public function __construct(array $data = [])
  {
    $required = ['pickup_date', 'pickup_time_from', 'pickup_time_to', 'delivery_date', 'delivery_time_from', 'delivery_time_to', 'pickup', 'delivery', 'packages'];
    foreach ($required as $field) {
      if (!array_key_exists($field, $data)) {
        throw new \InvalidArgumentException('QuoteRequest requires ' . $field . '.');
      }
    }

    $this->data = $this->normalizePayload($data);
  }

  /**
   * @return array<string, mixed>
   */
  public function toArray(): array
  {
    return $this->data;
  }

  /**
   * @param array<string, mixed> $data
   * @return array<string, mixed>
   */
  private function normalizePayload(array $data): array
  {
    $normalized = [];

    foreach ($data as $key => $value) {
      $normalized[$key] = $this->normalizeValue($value);
    }

    return $normalized;
  }

  private function normalizeValue(mixed $value): mixed
  {
    if ($value instanceof self) {
      return $value->toArray();
    }

    if ($value instanceof \ArrayObject) {
      return $this->normalizeValue($value->getArrayCopy());
    }

    if (is_array($value)) {
      $normalized = [];
      foreach ($value as $itemKey => $itemValue) {
        $normalized[$itemKey] = $this->normalizeValue($itemValue);
      }

      return $normalized;
    }

    if (is_object($value) && method_exists($value, 'toArray')) {
      return $this->normalizeValue($value->toArray());
    }

    return $value;
  }
}
