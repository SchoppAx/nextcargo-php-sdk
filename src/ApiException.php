<?php

namespace NexCargo;

use RuntimeException;
use Throwable;

class ApiException extends RuntimeException
{
  private ?string $apiCode;

  /** @var array<string, mixed> */
  private array $details;

  /**
   * @param array<string, mixed> $details
   */
  public function __construct(
    string $message = '',
    int $code = 0,
    ?Throwable $previous = null,
    ?string $apiCode = null,
    array $details = []
  ) {
    parent::__construct($message, $code, $previous);
    $this->apiCode = $apiCode;
    $this->details = $details;
  }

  public function getApiCode(): ?string
  {
    return $this->apiCode;
  }

  /**
   * @return array<string, mixed>
   */
  public function getDetails(): array
  {
    return $this->details;
  }
}
