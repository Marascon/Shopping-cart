<?php
declare(strict_types=1);

namespace Marascon\ShoppingCart\Cart\Exceptions;

use Exception;
use Throwable;

class StorageException extends Exception
{
    /**
     * @var string|null The specific operation that caused the error
     */
    private ?string $operation;

    /**
     * StorageException constructor.
     *
     * @param string $message
     * @param int $code
     * @param string|null $operation
     * @param Throwable|null $previous
     */
    public function __construct(string $message, int $code = 0, ?string $operation = null, ?Throwable $previous = null)
    {
        $this->operation = $operation;
        // Pass the message, code, and previous exception to the parent constructor
        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the operation that caused the exception (if available).
     *
     * @return string|null The operation that failed
     */
    public function getOperation(): ?string
    {
        return $this->operation;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $operationDetails = $this->operation ? " Operation: {$this->operation}." : '';
        return "[StorageException] {$this->message} in {$this->file} on line {$this->line}.{$operationDetails}";
    }
}
