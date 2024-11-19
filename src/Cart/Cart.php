<?php
declare(strict_types=1);

namespace Marascon\ShoppingCart\Cart;

use Marascon\ShoppingCart\Cart\Discount\DiscountInterface;
use Marascon\ShoppingCart\Config\Config;
use Marascon\ShoppingCart\Exceptions\ProductNotFoundException;
use Marascon\ShoppingCart\Product\Product;
use Marascon\ShoppingCart\Storage\StorageInterface;

class Cart
{
    private array $products = [];
    private array $listeners = [];
    private ?DiscountInterface $discount = null;
    private Config $config;
    private StorageInterface $storage;

    public function __construct(Config $config, StorageInterface $storage)
    {
        $this->config = $config;
        $this->storage = $storage;
    }

    /**
     * @param string $event
     * @param mixed $data
     */
    private function dispatchEvent(string $event, mixed $data): void
    {
        // Dispatch event only if listeners are attached
        foreach ($this->listeners as $listener) {
            $listener($event, $data);
        }
    }

    /**
     * Add a listener for events.
     *
     * @param callable $listener
     */
    public function addListener(callable $listener): void
    {
        $this->listeners[] = $listener;
    }

    /**
     * Add product to the cart.
     *
     * @param Product $product
     */
    public function addProduct(Product $product): void
    {
        $articleNumber = $product->getArticleNumber();
        $this->products[$articleNumber] = $this->getProduct($articleNumber)
            ? $this->getProduct($articleNumber)->withQuantity($this->getProduct($articleNumber)->getQuantity() + $product->getQuantity())
            : $product;

        $this->saveAndDispatchEvent('product.added', $product);
    }

    /**
     * Get all products in the cart.
     *
     * @return Product[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    /**
     * Remove product from cart by article number.
     *
     * @param string $articleNumber
     * @throws ProductNotFoundException
     */
    public function removeProduct(string $articleNumber): void
    {
        $product = $this->getProduct($articleNumber);
        if (!$product) {
            throw new ProductNotFoundException($articleNumber);
        }

        unset($this->products[$articleNumber]);
        $this->saveAndDispatchEvent('product.removed', $articleNumber);
    }

    /**
     * Update the quantity of a product.
     *
     * @param string $articleNumber
     * @param int $quantity
     * @throws ProductNotFoundException
     */
    public function updateQuantity(string $articleNumber, int $quantity): void
    {
        $product = $this->getProduct($articleNumber);
        if (!$product) {
            throw new ProductNotFoundException($articleNumber);
        }

        $this->products[$articleNumber] = $product->withQuantity($quantity);
        $this->saveAndDispatchEvent('product.updated', $this->products[$articleNumber]);
    }

    /**
     * Clear all products in the cart.
     */
    public function clearCart(): void
    {
        $this->products = [];
        $this->saveAndDispatchEvent('cart.cleared', null);
    }

    /**
     * Set a discount for the cart.
     *
     * @param DiscountInterface $discount
     */
    public function setDiscount(DiscountInterface $discount): void
    {
        $this->discount = $discount;
        $this->dispatchEvent('discount.set', $discount);
    }

    /**
     * Calculate the total price excluding VAT.
     *
     * @return float
     */
    public function calculateTotalPriceWithoutVat(): float
    {
        return $this->applyDiscountToTotalPrice(
            array_sum(array_map(fn($product) => $product->getPrice() * $product->getQuantity(), $this->products))
        );
    }

    /**
     * Calculate the total price including VAT.
     *
     * @return float
     */
    public function calculateTotalPriceWithVat(): float
    {
        $totalPriceWithoutVat = $this->calculateTotalPriceWithoutVat();
        return $totalPriceWithoutVat * (1 + $this->config->getVatRate() / 100);
    }

    /**
     * Save the current state of the cart.
     */
    private function save(): void
    {
        $this->storage->save($this->products);
    }

    /**
     * Load the cart state from storage.
     */
    public function load(): void
    {
        $this->products = $this->storage->load() ?? [];
    }

    /**
     * Get a product by its article number.
     *
     * @param string $articleNumber
     * @return Product|null
     */
    private function getProduct(string $articleNumber): ?Product
    {
        return $this->products[$articleNumber] ?? null;
    }

    /**
     * Apply discount to the total price.
     *
     * @param float $totalPrice
     * @return float
     */
    private function applyDiscountToTotalPrice(float $totalPrice): float
    {
        return $this->discount ? $this->discount->apply($totalPrice) : $totalPrice;
    }

    /**
     * Save the cart and dispatch event in a single method.
     *
     * @param string $event
     * @param mixed $data
     */
    private function saveAndDispatchEvent(string $event, mixed $data): void
    {
        $this->save();
        $this->dispatchEvent($event, $data);
    }
}
