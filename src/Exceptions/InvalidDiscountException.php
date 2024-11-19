<?php
declare(strict_types=1);

namespace Marascon\ShoppingCart\Exceptions;

use InvalidArgumentException;
use Throwable;

class InvalidDiscountException extends InvalidArgumentException
{
    /**
     * InvalidDiscountException constructor.
     *
     * @param string $message The exception message.
     * @param int $code The exception code (optional, default: 0).
     * @param Throwable|null $previous The previous exception (optional).
     */
    public function __construct(string $message, int $code = 0, Throwable $previous = null)
    {
        // Ensure the message is passed, and if necessary, format it in a standard way
        $formattedMessage = $this->formatMessage($message);

        // Pass the formatted message along with code and previous exception to the parent constructor
        parent::__construct($formattedMessage, $code, $previous);
    }

    /**
     * Format the exception message in a consistent manner.
     *
     * @param string $message The original message.
     * @return string The formatted message.
     */
    private function formatMessage(string $message): string
    {
        // Example formatting: you could add a standard prefix or other identifiers to messages
        return "[InvalidDiscount] " . $message;
    }
}
