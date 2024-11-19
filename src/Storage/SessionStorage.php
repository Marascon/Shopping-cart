<?php
declare(strict_types=1);

namespace Marascon\ShoppingCart\Storage;

use Marascon\ShoppingCart\Exceptions\StorageException;

class SessionStorage implements StorageInterface
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * @param array $data
     * @return void
     * @throws StorageException if session save fails
     */
    public function save(array $data): void
    {
        try {
            // Attempt to serialize and save the data to the session
            $_SESSION['cart'] = serialize($data);
            if ($_SESSION['cart'] === false) {
                throw new StorageException('Failed to serialize data for session storage.', 0, null, 'save');
            }
        } catch (\Exception $e) {
            // Catch any issues and throw a StorageException with context
            throw new StorageException('An error occurred while saving data to session storage.', 0, $e, 'save');
        }
    }

    /**
     * @return array
     * @throws StorageException if session load fails
     */
    public function load(): array
    {
        try {
            if (isset($_SESSION['cart'])) {
                $data = unserialize($_SESSION['cart']);
                if ($data === false && $_SESSION['cart'] !== 'b:0;') {
                    throw new StorageException('Failed to unserialize data from session storage.', 0, null, 'load');
                }
                return $data;
            }
            return [];
        } catch (\Exception $e) {
            // Catch any issues and throw a StorageException with context
            throw new StorageException('An error occurred while loading data from session storage.', 0, $e, 'load');
        }
    }

    /**
     * Clears the session data for cart
     */
    public function clear(): void
    {
        try {
            unset($_SESSION['cart']);
        } catch (\Exception $e) {
            throw new StorageException('An error occurred while clearing session data.', 0, $e, 'clear');
        }
    }

    /**
     * Deletes specific key from session data
     */
    public function delete(string $key): void
    {
        try {
            if (isset($_SESSION[$key])) {
                unset($_SESSION[$key]);
            }
        } catch (\Exception $e) {
            throw new StorageException('An error occurred while deleting data from session storage.', 0, $e, 'delete');
        }
    }
}

