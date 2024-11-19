# Shopping Cart

A simple and flexible shopping cart implementation in PHP that supports products, categories, VAT calculation, discounts, and storage via session-based persistence.

## Features

- **Product Management**: Add, remove, and update quantities of products.
- **Discounts**: Apply percentage-based discounts to the cart.
- **VAT Calculation**: Automatically calculate total prices with and without VAT.
- **Storage**: Session-based storage for persisting cart data across page reloads.
- **Event Listeners**: Dispatch events (e.g., product added, cart cleared) and register listeners to respond to these events.

## Requirements
- PHP 8.3
- Composer for managing dependencies

## Installation
as this package is not published on Packagist there is a bit of manual labor involved to install the package

1. Add Repositories to your Composer.json and require the package

```json
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/marascon/shopping-cart"
    }
  ]
 ```
```json
  "require": {
    "marascon/shopping-cart": "^1"
  }
```

2. Install the dependencies using Composer:
```bash
  composer install
```


# Usage
## Creating a Cart

Start a PHP session and create a cart instance with a configuration for VAT and currency.

```PHP
<?php
use Marascon\ShoppingCart\Product\Product;
use Marascon\ShoppingCart\Product\ProductCategory;
use Marascon\ShoppingCart\Cart\Cart;
use Marascon\ShoppingCart\Storage\SessionStorage;
use Marascon\ShoppingCart\Config\Config;

// Create a session-based storage
$storage = new SessionStorage();

// Create a config with VAT and currency settings
$config = new Config('EUR', 21.0); // 21% VAT rate

// Create or load the cart instance
$cart = new Cart($config, $storage);

// Load cart from session storage
$cart->load();
```

## Adding Products
```PHP
$product1 = new Product('001', 'Laptop', 1500, ProductCategory::Electronics, 2);
$product2 = new Product('002', 'Smartphone', 800, ProductCategory::Electronics, 1);
$product3 = new Product('003', 'Coffee Maker', 120, ProductCategory::HomeAppliances, 3);

$cart->addProduct($product1);
$cart->addProduct($product2);
$cart->addProduct($product3);
```

## Updating Product Quantity
```PHP
$cart->updateQuantity('002', 5);
```

## Removing Products
```PHP
$cart->removeProduct('003');
```

## Applying Discounts

```PHP
use Marascon\ShoppingCart\Cart\Discount\PercentageDiscount;

$cart->setDiscount(new PercentageDiscount(10)); // 10% discount
```

## Calculating Total Price
```PHP
echo "Total price (no VAT): " . number_format($cart->calculateTotalPriceWithoutVat(), 2) . " EUR</br>";
echo "Total price (with VAT): " . number_format($cart->calculateTotalPriceWithVat(), 2) . " EUR</br>";
```

## Clearing the Cart
```PHP
$cart->clearCart();
```

## Event Listeners
Example for printing, this can be used for event queueing and other things as well
```PHP
function registerCartListeners(Cart $cart): void
{
    $cart->addListener(function ($event, $data) {
        switch ($event) {
            case 'product.added':
                echo "<code><b>Listener:</b> Product added - " . $data->getDescription() . " (" . $data->getQuantity() . " units with a price of ". $data->getPrice() ." each).</code> </br>";
                break;
            case 'product.removed':
                echo "<code><b>Listener:</b> Product removed - Article number: $data.</code> </br>";
                break;
            case 'product.updated':
                echo "<code><b>Listener:</b> Product updated - " . $data->getDescription() . " (new quantity: " . $data->getQuantity() . ").</code> </br>";
                break;
            case 'cart.cleared':
                echo "<code><b>Listener:</b> The cart has been cleared.</code> </br>";
                break;
            case 'discount.set':
                echo "<code><b>Listener:</b> A discount of " . $data->getDiscountPercentage() . "% has been applied.</code> </br>";
                break;
            default:
                echo "<code><b>Listener:</b> Unknown event: $event.</code> </br>";
        }
    });
}

// Register the listener
registerCartListeners($cart);

```

## Running tests
Cloe the repository locally, and install composer dependency's
```bash
Git clone https://github.com/marascon/shopping-cart
cd Shopping-cart
composer install
```

Execute unit tests after `composer install`
```
composer test
```

List available tests
```
composer list-tests
``` 

## Project Structure

`src/`: Contains the core logic for the shopping cart, products, discounts, and storage.

`tests/`: PHPUnit tests for the shopping cart functionality.

`vendor/`: Composer-managed dependencies.

## License
This project is licensed under the MIT License 