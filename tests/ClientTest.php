<?php

namespace NexCargo\Tests;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use NexCargo\Client;
use NexCargo\Models\Address;
use NexCargo\Models\Dimensions;
use NexCargo\Models\Parcel;
use NexCargo\Models\QuoteRequest;
use NexCargo\Models\ShipmentRequest;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
  public function testCreateShipmentSendsRequestToExpectedEndpoint(): void
  {
    /** @var array<int, array<string, mixed>> $history */
    $history = [];
    $mock = new MockHandler([
      new Response(200, [], '{"id":"abc123"}'),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $handlerStack->push(Middleware::history($history));
    $guzzle = new GuzzleClient(['handler' => $handlerStack]);
    $client = new Client('test-key', 'https://api.example.com', $guzzle);

    $shipmentRequest = new ShipmentRequest([
      'carrier' => 'auto',
      'pickup_date' => '2026-01-15',
      'pickup_time_from' => '09:00',
      'pickup_time_to' => '12:00',
      'delivery_date' => '2026-01-16',
      'delivery_time_from' => '08:00',
      'delivery_time_to' => '18:00',
      'pickup' => [
        'name' => 'Musterfirma GmbH',
        'street' => 'Musterstraße',
        'house_number' => '123',
        'zipcode' => '50667',
        'city' => 'Köln',
        'country' => 'DE',
      ],
      'delivery' => [
        'name' => 'Empfänger GmbH',
        'street' => 'Beispielweg',
        'house_number' => '456',
        'zipcode' => '10115',
        'city' => 'Berlin',
        'country' => 'DE',
      ],
      'packages' => [
        [
          'weight' => 15.5,
          'length' => 60,
          'width' => 40,
          'height' => 30,
        ],
      ],
    ]);
    $response = $client->createShipment($shipmentRequest);

    $this->assertSame(200, $response->getStatusCode());
    $this->assertTrue(isset($history[0]));
    $this->assertSame('POST', $history[0]['request']->getMethod());
    $this->assertSame('/api/v1/shipments', (string) $history[0]['request']->getUri()->getPath());
    $this->assertSame('{"carrier":"auto","pickup_date":"2026-01-15","pickup_time_from":"09:00","pickup_time_to":"12:00","delivery_date":"2026-01-16","delivery_time_from":"08:00","delivery_time_to":"18:00","pickup":{"name":"Musterfirma GmbH","street":"Musterstra\u00dfe","house_number":"123","zipcode":"50667","city":"K\u00f6ln","country":"DE"},"delivery":{"name":"Empf\u00e4nger GmbH","street":"Beispielweg","house_number":"456","zipcode":"10115","city":"Berlin","country":"DE"},"packages":[{"weight":15.5,"length":60,"width":40,"height":30}]}', (string) $history[0]['request']->getBody());
  }

  public function testGetShipmentLabelUsesLabelEndpoint(): void
  {
    $history = [];
    $mock = new MockHandler([
      new Response(200, [], 'pdf-content'),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $handlerStack->push(Middleware::history($history));
    $guzzle = new GuzzleClient(['handler' => $handlerStack]);
    $client = new Client('test-key', 'https://api.example.com', $guzzle);

    $response = $client->getShipmentLabel('abc123');

    $this->assertSame(200, $response->getStatusCode());
    $this->assertSame('GET', $history[0]['request']->getMethod());
    $this->assertSame('/api/v1/shipments/abc123/label', (string) $history[0]['request']->getUri()->getPath());
  }

  public function testGetShipmentTrackingUsesTrackingEndpoint(): void
  {
    $history = [];
    $mock = new MockHandler([
      new Response(200, [], '{"status":"in_transit"}'),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $handlerStack->push(Middleware::history($history));
    $guzzle = new GuzzleClient(['handler' => $handlerStack]);
    $client = new Client('test-key', 'https://api.example.com', $guzzle);

    $response = $client->getShipmentTracking('abc123');

    $this->assertSame(200, $response->getStatusCode());
    $this->assertSame('/api/v1/shipments/abc123/tracking', (string) $history[0]['request']->getUri()->getPath());
  }

  public function testCancelShipmentUsesDeleteEndpoint(): void
  {
    $history = [];
    $mock = new MockHandler([
      new Response(204, []),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $handlerStack->push(Middleware::history($history));
    $guzzle = new GuzzleClient(['handler' => $handlerStack]);
    $client = new Client('test-key', 'https://api.example.com', $guzzle);

    $response = $client->cancelShipment('abc123');

    $this->assertSame(204, $response->getStatusCode());
    $this->assertSame('DELETE', $history[0]['request']->getMethod());
  }

  public function testQuoteShipmentUsesQuoteEndpoint(): void
  {
    $history = [];
    $mock = new MockHandler([
      new Response(200, [], '{"amount": 42.5}'),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $handlerStack->push(Middleware::history($history));
    $guzzle = new GuzzleClient(['handler' => $handlerStack]);
    $client = new Client('test-key', 'https://api.example.com', $guzzle);

    $quoteRequest = new QuoteRequest([
      'carrier' => 'auto',
      'pickup_date' => '2026-01-15',
      'pickup_time_from' => '09:00',
      'pickup_time_to' => '12:00',
      'delivery_date' => '2026-01-16',
      'delivery_time_from' => '08:00',
      'delivery_time_to' => '18:00',
      'pickup' => [
        'name' => 'Musterfirma GmbH',
        'street' => 'Musterstraße',
        'house_number' => '123',
        'zipcode' => '50667',
        'city' => 'Köln',
        'country' => 'DE',
      ],
      'delivery' => [
        'name' => 'Empfänger GmbH',
        'street' => 'Beispielweg',
        'house_number' => '456',
        'zipcode' => '10115',
        'city' => 'Berlin',
        'country' => 'DE',
      ],
      'packages' => [
        [
          'weight' => 15.5,
          'length' => 60,
          'width' => 40,
          'height' => 30,
        ],
      ],
    ]);
    $response = $client->quoteShipment($quoteRequest);

    $this->assertSame(200, $response->getStatusCode());
    $this->assertSame('/api/v1/shipments/quote', (string) $history[0]['request']->getUri()->getPath());
    $this->assertSame('{"carrier":"auto","pickup_date":"2026-01-15","pickup_time_from":"09:00","pickup_time_to":"12:00","delivery_date":"2026-01-16","delivery_time_from":"08:00","delivery_time_to":"18:00","pickup":{"name":"Musterfirma GmbH","street":"Musterstra\u00dfe","house_number":"123","zipcode":"50667","city":"K\u00f6ln","country":"DE"},"delivery":{"name":"Empf\u00e4nger GmbH","street":"Beispielweg","house_number":"456","zipcode":"10115","city":"Berlin","country":"DE"},"packages":[{"weight":15.5,"length":60,"width":40,"height":30}]}', (string) $history[0]['request']->getBody());
  }

  public function testCreateShipmentSerializesNestedPayloadModels(): void
  {
    $history = [];
    $mock = new MockHandler([
      new Response(200, [], '{"id":"abc123"}'),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $handlerStack->push(Middleware::history($history));
    $guzzle = new GuzzleClient(['handler' => $handlerStack]);
    $client = new Client('test-key', 'https://api.example.com', $guzzle);

    $shipmentRequest = new ShipmentRequest([
      'carrier' => 'auto',
      'pickup_date' => '2026-01-15',
      'pickup_time_from' => '09:00',
      'pickup_time_to' => '12:00',
      'delivery_date' => '2026-01-16',
      'delivery_time_from' => '08:00',
      'delivery_time_to' => '18:00',
      'pickup' => new Address([
        'name' => 'Musterfirma GmbH',
        'street' => 'Musterstraße',
        'house_number' => '123',
        'zipcode' => '50667',
        'city' => 'Köln',
        'country' => 'DE',
      ]),
      'delivery' => new Address([
        'name' => 'Empfänger GmbH',
        'street' => 'Beispielweg',
        'house_number' => '456',
        'zipcode' => '10115',
        'city' => 'Berlin',
        'country' => 'DE',
      ]),
      'packages' => [
        new Parcel([
          'weight' => 15.5,
          'dimensions' => new Dimensions(['length' => 60, 'width' => 40, 'height' => 30]),
        ]),
      ],
    ]);

    $client->createShipment($shipmentRequest);

    $this->assertSame([
      'carrier' => 'auto',
      'pickup_date' => '2026-01-15',
      'pickup_time_from' => '09:00',
      'pickup_time_to' => '12:00',
      'delivery_date' => '2026-01-16',
      'delivery_time_from' => '08:00',
      'delivery_time_to' => '18:00',
      'pickup' => [
        'name' => 'Musterfirma GmbH',
        'street' => 'Musterstraße',
        'house_number' => '123',
        'zipcode' => '50667',
        'city' => 'Köln',
        'country' => 'DE',
      ],
      'delivery' => [
        'name' => 'Empfänger GmbH',
        'street' => 'Beispielweg',
        'house_number' => '456',
        'zipcode' => '10115',
        'city' => 'Berlin',
        'country' => 'DE',
      ],
      'packages' => [
        [
          'weight' => 15.5,
          'dimensions' => ['length' => 60, 'width' => 40, 'height' => 30],
        ],
      ],
    ], json_decode((string) $history[0]['request']->getBody(), true));
  }

  public function testQuoteShipmentSerializesNestedPayloadModels(): void
  {
    $history = [];
    $mock = new MockHandler([
      new Response(200, [], '{"amount": 42.5}'),
    ]);

    $handlerStack = HandlerStack::create($mock);
    $handlerStack->push(Middleware::history($history));
    $guzzle = new GuzzleClient(['handler' => $handlerStack]);
    $client = new Client('test-key', 'https://api.example.com', $guzzle);

    $quoteRequest = new QuoteRequest([
      'carrier' => 'auto',
      'pickup_date' => '2026-01-15',
      'pickup_time_from' => '09:00',
      'pickup_time_to' => '12:00',
      'delivery_date' => '2026-01-16',
      'delivery_time_from' => '08:00',
      'delivery_time_to' => '18:00',
      'pickup' => new Address([
        'name' => 'Musterfirma GmbH',
        'street' => 'Musterstraße',
        'house_number' => '123',
        'zipcode' => '50667',
        'city' => 'Köln',
        'country' => 'DE',
      ]),
      'delivery' => new Address([
        'name' => 'Empfänger GmbH',
        'street' => 'Beispielweg',
        'house_number' => '456',
        'zipcode' => '10115',
        'city' => 'Berlin',
        'country' => 'DE',
      ]),
      'packages' => [
        new Parcel([
          'weight' => 15.5,
          'dimensions' => new Dimensions(['length' => 60, 'width' => 40, 'height' => 30]),
        ]),
      ],
    ]);

    $client->quoteShipment($quoteRequest);

    $this->assertSame([
      'carrier' => 'auto',
      'pickup_date' => '2026-01-15',
      'pickup_time_from' => '09:00',
      'pickup_time_to' => '12:00',
      'delivery_date' => '2026-01-16',
      'delivery_time_from' => '08:00',
      'delivery_time_to' => '18:00',
      'pickup' => [
        'name' => 'Musterfirma GmbH',
        'street' => 'Musterstraße',
        'house_number' => '123',
        'zipcode' => '50667',
        'city' => 'Köln',
        'country' => 'DE',
      ],
      'delivery' => [
        'name' => 'Empfänger GmbH',
        'street' => 'Beispielweg',
        'house_number' => '456',
        'zipcode' => '10115',
        'city' => 'Berlin',
        'country' => 'DE',
      ],
      'packages' => [
        [
          'weight' => 15.5,
          'dimensions' => ['length' => 60, 'width' => 40, 'height' => 30],
        ],
      ],
    ], json_decode((string) $history[0]['request']->getBody(), true));
  }

  public function testCreateShipmentRequiresRequiredFields(): void
  {
    $this->expectException(\InvalidArgumentException::class);
    $this->expectExceptionMessage('ShipmentRequest requires carrier.');

    new ShipmentRequest([]);
  }

  public function testQuoteShipmentRequiresRequiredFields(): void
  {
    $this->expectException(\InvalidArgumentException::class);
    $this->expectExceptionMessage('QuoteRequest requires pickup_date.');

    new QuoteRequest([]);
  }
}
