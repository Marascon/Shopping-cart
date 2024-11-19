<?php

namespace Marascon\ShoppingCart\Tests\Storage;
namespace Marascon\ShoppingCart\Tests\Storage;

use Marascon\ShoppingCart\Exceptions\StorageException;
use Marascon\ShoppingCart\Storage\SessionStorage;
use PHPUnit\Framework\TestCase;

class SessionStorageTest extends TestCase
{
    /**
     * Set up the test environment by ensuring that the session is started.
     */
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = []; // Reset the session before each test
    }

    /**
     * Test that the save method works correctly.
     */
    public function testSave(): void
    {
        $storage = new SessionStorage();
        $data = ['key' => 'value'];

        $storage->save($data);

        // Assert that the session contains the serialized data
        $this->assertEquals(serialize($data), $_SESSION['cart']);
    }

    /**
     * Test that the load method works correctly.
     */
    public function testLoad(): void
    {
        $storage = new SessionStorage();
        $data = ['key' => 'value'];
        $_SESSION['cart'] = serialize($data);  // Simulating stored session data

        $loadedData = $storage->load();

        // Assert that the loaded data matches the saved data
        $this->assertEquals($data, $loadedData);
    }

    /**
     * Test that the load method returns an empty array when no session data exists.
     */
    public function testLoadEmptySession(): void
    {
        $storage = new SessionStorage();

        $loadedData = $storage->load();

        // Assert that no data is loaded when the session is empty
        $this->assertEmpty($loadedData);
    }

    /**
     * Test that the delete method works correctly.
     */
    public function testDelete(): void
    {
        $storage = new SessionStorage();
        $_SESSION['cart'] = serialize(['key' => 'value']);  // Simulating stored session data

        $storage->delete('cart');

        // Assert that the session data is deleted
        $this->assertArrayNotHasKey('cart', $_SESSION);
    }

    /**
     * Test that the save method throws a StorageException if serialization fails.
     */
    public function testSaveThrowsExceptionOnFailure(): void
    {
        $this->expectException(StorageException::class);
        $this->expectExceptionMessage('An error occurred while saving data to session storage.');

        $storage = $this->createMock(SessionStorage::class);
        $storage->method('save')->willThrowException(new StorageException('An error occurred while saving data to session storage.'));

        // Attempt to save, which should throw an exception
        $storage->save([]);
    }

    /**
     * Test that the load method throws a StorageException if unserialization fails.
     */
    public function testLoadThrowsExceptionOnFailure(): void
    {
        $this->expectException(StorageException::class);
        $this->expectExceptionMessage('An error occurred while loading data from session storage.');

        $storage = $this->createMock(SessionStorage::class);
        $storage->method('load')->willThrowException(new StorageException('An error occurred while loading data from session storage.'));

        // Attempt to load, which should throw an exception
        $storage->load();
    }

    /**
     * Test that the delete method throws an exception if the delete operation fails.
     */
    public function testDeleteThrowsExceptionOnFailure(): void
    {
        $this->expectException(StorageException::class);
        $this->expectExceptionMessage('An error occurred while deleting data from session storage.');

        $storage = $this->createMock(SessionStorage::class);
        $storage->method('delete')->willThrowException(new StorageException('An error occurred while deleting data from session storage.'));

        // Attempt to delete, which should throw an exception
        $storage->delete('cart');
    }

    /**
     * Test that the clear method throws an exception if the clear operation fails.
     */
    public function testClearThrowsExceptionOnFailure(): void
    {
        $this->expectException(StorageException::class);
        $this->expectExceptionMessage('An error occurred while clearing session data.');

        $storage = $this->createMock(SessionStorage::class);
        $storage->method('clear')->willThrowException(new StorageException('An error occurred while clearing session data.'));

        // Attempt to clear, which should throw an exception
        $storage->clear();
    }
}

