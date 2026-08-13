<?php

namespace NexCargo\Tests\Models;

use NexCargo\Models\Address;
use NexCargo\Models\Recipient;
use PHPUnit\Framework\TestCase;

class RecipientTest extends TestCase
{
  public function testToArrayWithNameOnly(): void
  {
    $recipient = new Recipient(['name' => 'Max Mustermann']);

    $this->assertSame(['name' => 'Max Mustermann'], $recipient->toArray());
  }

  public function testToArrayWithAddress(): void
  {
    $address = new Address(['name' => 'Test GmbH', 'country' => 'DE']);
    $recipient = new Recipient(['name' => 'Max Mustermann', 'address' => $address]);

    $result = $recipient->toArray();

    $this->assertSame('Max Mustermann', $result['name']);
    $this->assertSame(['name' => 'Test GmbH', 'country' => 'DE'], $result['address']);
  }

  public function testToArrayWithNoFieldsIsEmpty(): void
  {
    $this->assertSame([], (new Recipient())->toArray());
  }
}
