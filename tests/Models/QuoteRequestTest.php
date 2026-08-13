<?php

namespace NexCargo\Tests\Models;

use NexCargo\Models\QuoteRequest;
use NexCargo\Models\Address;
use NexCargo\Tests\Fixtures;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class QuoteRequestTest extends TestCase
{
  /** @return array<string, array{0: string}> */
  public static function requiredFieldsProvider(): array
  {
    return [
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
    $this->expectExceptionMessageIs('QuoteRequest requires ' . $field . '.');

    $data = Fixtures::quoteData();
    unset($data[$field]);
    new QuoteRequest($data);
  }

  public function testToArrayReturnsNormalizedData(): void
  {
    $request = new QuoteRequest(Fixtures::quoteData());
    $result = $request->toArray();

    $this->assertSame('2026-01-15', $result['pickup_date']);
    $this->assertIsArray($result['pickup']);
    $this->assertIsArray($result['packages']);
  }

  public function testNormalizesNestedSelfInstance(): void
  {
    $inner = new QuoteRequest(Fixtures::quoteData());
    $data = Fixtures::quoteData();
    $data['related'] = $inner;

    $result = (new QuoteRequest($data))->toArray();

    $this->assertIsArray($result['related']);
    $this->assertSame('2026-01-15', $result['related']['pickup_date']);
  }

  public function testNormalizesArrayObject(): void
  {
    $data = Fixtures::quoteData();
    $data['metadata'] = new \ArrayObject(['source' => 'test']);

    $result = (new QuoteRequest($data))->toArray();

    $this->assertSame(['source' => 'test'], $result['metadata']);
  }

  public function testNormalizesGenericObjectWithToArrayMethod(): void
  {
    $data = Fixtures::quoteData();
    $data['pickup'] = new Address(Fixtures::addressData());

    $result = (new QuoteRequest($data))->toArray();

    $this->assertSame(Fixtures::addressData(), $result['pickup']);
  }
}
