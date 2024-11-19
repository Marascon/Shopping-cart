<?php

namespace Marascon\ShoppingCart\Tests\Cart\Exceptions;

use Marascon\ShoppingCart\Cart\Exceptions\ProductNotFoundException;
use PHPUnit\Framework\TestCase;

class ProductNotFoundExceptionTest extends TestCase
{
    public function testExceptionMessage(): void
    {
        $articleNumber = '999';
        // Create an exception with a specific article number ('999')
        $exception = new ProductNotFoundException($articleNumber);

        // Assert that the exception message matches the expected format
        $this->assertSame("Product with article number $articleNumber not found.", $exception->getMessage());
    }

    public function testExceptionCode(): void
    {
        $articleNumber = '999';
        // Create an exception with a specific article number ('999')
        $exception = new ProductNotFoundException($articleNumber);

        // Assert that the exception code is 0 (default value)
        $this->assertSame(0, $exception->getCode());
    }
}
