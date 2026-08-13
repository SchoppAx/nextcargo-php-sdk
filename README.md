# NexCargo PHP SDK

A PHP 8.2+ SDK for the Camel Cloud API / NexCargo carrier integration using Guzzle.

[![Latest Stable Version](https://img.shields.io/github/v/release/schoppax/nextcargo-php-sdk?label=Stable)](https://github.com/schoppax/nextcargo-php-sdk/releases)
[![Linter](https://github.com/schoppax/nextcargo-php-sdk/actions/workflows/linter.yml/badge.svg?branch=main)](https://github.com/schoppax/nextcargo-php-sdk/actions/workflows/linter.yml)
[![Code style](https://github.com/schoppax/nextcargo-php-sdk/actions/workflows/phpstan.yml/badge.svg?branch=main)](https://github.com/schoppax/nextcargo-php-sdk/actions/workflows/phpstan.yml)
[![Test](https://github.com/schoppax/nextcargo-php-sdk/actions/workflows/test.yml/badge.svg?branch=main)](https://github.com/schoppax/nextcargo-php-sdk/actions/workflows/test.yml)
[![Coverage Status](https://raw.githubusercontent.com/schoppax/nextcargo-php-sdk/badges/coverage.svg)](https://github.com/schoppax/nextcargo-php-sdk)

![Minimum PHP Version](https://img.shields.io/packagist/dependency-v/schoppax/nexcargo-php-sdk/php.svg)
![Tested on PHP 8.2 to 8.5](https://img.shields.io/badge/tested%20on-PHP%208.2%20|%208.3%20|%208.4%20|%208.5-brightgreen.svg?maxAge=2419200)

## Installation

Install the package from Packagist:

```bash
composer require schoppax/nexcargo-php-sdk
```

For local development, install the dependencies and configure the project with:

```bash
composer setup
```

## API overview

The SDK targets the following endpoints from the original API documentation:

- `POST /api/v1/shipments` for creating a shipment
- `POST /api/v1/shipments/quote` for requesting a quote
- `GET /api/v1/shipments/{id}/label` for retrieving a label
- `GET /api/v1/shipments/{id}/tracking` for tracking information
- `DELETE /api/v1/shipments/{id}` for cancelling a shipment

## Usage

You can build requests either with serialized model objects or with plain payload arrays. Both styles are accepted by the client methods.

```php
<?php

use NexCargo\Client;
use NexCargo\Models\Address;
use NexCargo\Models\Dimensions;
use NexCargo\Models\Parcel;
use NexCargo\Models\ShipmentRequest;
use NexCargo\Models\QuoteRequest;

$client = new Client('your-api-key', 'https://api.example.com');
```

## Shipment creation

The shipment endpoint expects a payload with the core booking fields and nested pickup, delivery and package data.

```php
$pickup = new Address([
    'name' => 'Musterfirma GmbH',
    'street' => 'Musterstraße',
    'house_number' => '123',
    'zipcode' => '50667',
    'city' => 'Köln',
    'country' => 'DE',
]);

$delivery = new Address([
    'name' => 'Empfänger GmbH',
    'street' => 'Beispielweg',
    'house_number' => '456',
    'zipcode' => '10115',
    'city' => 'Berlin',
    'country' => 'DE',
]);

$package = new Parcel([
    'weight' => 15.5,
    'dimensions' => new Dimensions([
        'length' => 60,
        'width' => 40,
        'height' => 30,
    ]),
]);

$shipment = new ShipmentRequest([
    'carrier' => 'auto',
    'pickup_date' => '2026-01-15',
    'pickup_time_from' => '09:00',
    'pickup_time_to' => '12:00',
    'delivery_date' => '2026-01-16',
    'delivery_time_from' => '08:00',
    'delivery_time_to' => '18:00',
    'pickup' => $pickup,
    'delivery' => $delivery,
    'packages' => [$package],
]);

$shipmentResponse = $client->createShipment($shipment);
```

## Quote request

The quote endpoint uses the same request shape as shipment creation, but the carrier field is optional.

```php
$quote = new QuoteRequest([
    'pickup_date' => '2026-01-15',
    'pickup_time_from' => '09:00',
    'pickup_time_to' => '12:00',
    'delivery_date' => '2026-01-16',
    'delivery_time_from' => '08:00',
    'delivery_time_to' => '18:00',
    'pickup' => $pickup,
    'delivery' => $delivery,
    'packages' => [$package],
]);

$quoteResponse = $client->quoteShipment($quote);
```

## Other endpoints

```php
$pdfContents = $client->getShipmentLabelContent($trackingNumber);
file_put_contents('shipment-label.pdf', $pdfContents);

$tracking = $client->getShipmentTracking($trackingNumber);
$cancelled = $client->cancelShipment($trackingNumber);
$cancellationMessage = $cancelled['data']['message'];
```
