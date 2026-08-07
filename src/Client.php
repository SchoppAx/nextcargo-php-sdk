<?php

namespace NexCargo;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use NexCargo\Models\QuoteRequest;
use NexCargo\Models\ShipmentRequest;
use Psr\Http\Message\ResponseInterface;

class Client
{
  private string $apiKey;
  private string $baseUri;
  private GuzzleClient $httpClient;

  public function __construct(string $apiKey, string $baseUri = 'https://api.camelcloud.example', ?GuzzleClient $httpClient = null)
  {
    $this->apiKey = $apiKey;
    $this->baseUri = rtrim($baseUri, '/') . '/';
    $this->httpClient = $httpClient ?? new GuzzleClient([
      'base_uri' => $this->baseUri,
      'headers' => [
        'Authorization' => 'Bearer ' . $this->apiKey,
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
      ],
    ]);
  }

  public function getHttpClient(): GuzzleClient
  {
    return $this->httpClient;
  }

  /**
   * @param array<string, mixed> $options
   */
  public function request(string $method, string $uri, array $options = []): ResponseInterface
  {
    try {
      return $this->httpClient->request($method, $uri, $options);
    } catch (GuzzleException $exception) {
      throw new ApiException($exception->getMessage(), $exception->getCode(), $exception);
    }
  }

  /**
   * @param ShipmentRequest|array<string, mixed> $payload
   */
  public function createShipment(ShipmentRequest|array $payload): ResponseInterface
  {
    $request = $payload instanceof ShipmentRequest ? $payload : new ShipmentRequest($payload);

    return $this->request('POST', '/api/v1/shipments', ['json' => $request->toArray()]);
  }

  public function getShipmentLabel(string $id): ResponseInterface
  {
    return $this->request('GET', '/api/v1/shipments/' . $id . '/label');
  }

  public function getShipmentTracking(string $id): ResponseInterface
  {
    return $this->request('GET', '/api/v1/shipments/' . $id . '/tracking');
  }

  public function cancelShipment(string $id): ResponseInterface
  {
    return $this->request('DELETE', '/api/v1/shipments/' . $id);
  }

  /**
   * @param QuoteRequest|array<string, mixed> $payload
   */
  public function quoteShipment(QuoteRequest|array $payload): ResponseInterface
  {
    $request = $payload instanceof QuoteRequest ? $payload : new QuoteRequest($payload);

    return $this->request('POST', '/api/v1/shipments/quote', ['json' => $request->toArray()]);
  }
}
