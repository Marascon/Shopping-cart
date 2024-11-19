<?php
declare(strict_types=1);

namespace Marascon\ShoppingCart\Storage;

use Marascon\ShoppingCart\Cart\Exceptions\StorageException;

interface StorageInterface
{
    /**
     * Saves the provided data to storage.
     *
     * @param array<string, mixed> $data Associative array of data to store
     * @return void
     * @throws StorageException If saving the data fails
     */
    public function save(array $data): void;

    /**
     * Loads data from storage.
     *
     * @return array<string, mixed> The data loaded from storage
     * @throws StorageException If loading the data fails
     */
    public function load(): array;

    /**
     * Clears the stored data from storage.
     *
     * @return void
     * @throws StorageException If clearing the data fails
     */
    public function clear(): void;

    /**
     * Deletes a specific item from the storage.
     *
     * @param string $key The key of the item to delete
     * @return void
     * @throws StorageException If the item cannot be deleted
     */
    public function delete(string $key): void;
}
