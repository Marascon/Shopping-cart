<?php

namespace Marascon\ShoppingCart\Tests\Cart\Exceptions;

use Marascon\ShoppingCart\Cart\Exceptions\StorageException;
use PHPUnit\Framework\TestCase;

class StorageExceptionTest extends TestCase
{
    /**
     * Test that StorageException is thrown with the correct message format.
     */
    public function testStorageExceptionWithMessage()
    {
        $errorMessage = "Failed to save data to session.";
        $operation = "Save operation";

        // Expect the StorageException to be thrown with the correct message format
        $this->expectException(StorageException::class);
        $this->expectExceptionMessage($errorMessage);

        // Simulate the exception being thrown
        throw new StorageException($errorMessage, 0, $operation);  // No previous exception (null)
    }

    /**
     * Test that the exception string output contains expected details (message, file, line, operation).
     */
    public function testStorageExceptionToString()
    {
        $errorMessage = "Failed to load data from session.";
        $operation = "Load operation";

        $exception = new StorageException($errorMessage, 0, $operation); // No previous exception (null)

        // Get the string representation of the exception
        $exceptionString = (string) $exception;

        // Assert that the exception string contains the expected message format
        $this->assertStringContainsString("[StorageException] {$errorMessage}", $exceptionString);
        $this->assertStringContainsString("in {$exception->getFile()}", $exceptionString);
        $this->assertStringContainsString("on line {$exception->getLine()}", $exceptionString);
        $this->assertStringContainsString("Operation: {$operation}.", $exceptionString);
    }

    /**
     * Test that the exception string does not contain the operation when it is null.
     */
    public function testStorageExceptionToStringWithoutOperation()
    {
        $errorMessage = "Failed to save data to session.";

        $exception = new StorageException($errorMessage, 0); // No operation, no previous exception (null)

        // Get the string representation of the exception
        $exceptionString = (string) $exception;

        // Assert that the exception string contains the expected message format but no operation
        $this->assertStringContainsString("[StorageException] {$errorMessage}", $exceptionString);
        $this->assertStringContainsString("in {$exception->getFile()}", $exceptionString);
        $this->assertStringContainsString("on line {$exception->getLine()}", $exceptionString);
        $this->assertStringNotContainsString("Operation:", $exceptionString);
    }
}
