<?php
require_once 'vendor/autoload.php';

use Marascon\ShoppingCart\Cart\Exceptions\InvalidDiscountException;
use Marascon\ShoppingCart\Product\Product;
use Marascon\ShoppingCart\Product\ProductCategory;
use Marascon\ShoppingCart\Cart\Cart;
use Marascon\ShoppingCart\Storage\SessionStorage;
use Marascon\ShoppingCart\Cart\Exceptions\ProductNotFoundException;
use Marascon\ShoppingCart\Cart\Discount\PercentageDiscount;
use Marascon\ShoppingCart\Config\Config;

// Create a session-based storage
$storage = new SessionStorage();

// Create a config with VAT and currency settings
$config = new Config('EUR', 21.0); // 21% VAT rate

// Create or load the cart instance
$cart = new Cart($config, $storage);

// Attach listeners
registerCartListeners($cart);

// Load cart from session storage
$cart->load();

echo "The currency is set to <b>" . $config->getCurrency() . "</b> and the VAT rate is set to <b>" . $config->getVatRate() . "%</b></br></br>";

// Add some products to the cart if it's empty (for demonstration purposes)
$product1 = new Product('001', 'Laptop', 1500, ProductCategory::Electronics, 2);
$product2 = new Product('002', 'Smartphone', 800, ProductCategory::Electronics, 1);
$product3 = new Product('003', 'Coffee Maker', 120, ProductCategory::HomeAppliances, 3);

$cart->addProduct($product1);
$cart->addProduct($product2);
$cart->addProduct($product3);

echo "Added initial products to the cart.</br></br>";

Echo "<b>Products in cart:</b></br>";
displayCart($cart, $config);

// Update quantity of product with ID '002'
echo "</br>Updating quantity of product '002' to 5...</br>";
try {
    $cart->updateQuantity('002', 5);
} catch (ProductNotFoundException $e) {
    echo "Product not found: " . $e->getMessage() . "</br>";
}

// Display updated cart
displayCart($cart, $config);

// Remove product with ID '003' from the cart
echo "</br>Removing product '003'...</br>";
try {
    $cart->removeProduct('003');
} catch (ProductNotFoundException $e) {
    echo "Product not found: " . $e->getMessage() . "</br>";
}

// Display cart after removal
echo "Products in the cart after removal:</br>";
displayCart($cart, $config);

// Apply a 10% discount to the cart
echo "</br>Applying 10% Discount...</br>";
try {
    $discount = new PercentageDiscount(10); // Valid positive discount
    $cart->setDiscount($discount);
} catch (InvalidDiscountException $e) {
    echo "</br><b>".$e->getMessage()."</b></br>";
}

// Display the Total price Basket after discount
echo "Total price Basket after discount (no VAT): " . number_format($cart->calculateTotalPriceWithoutVat(), 2) . " " . $config->getCurrency() . "</br>";
echo "Total price Basket after discount (with VAT): " . number_format($cart->calculateTotalPriceWithVat(), 2) . " " . $config->getCurrency() . "</br>";

// Apply a negative discount to the cart
echo "</br>Applying Negative(-10%) Discount...</br>";
try {
    $negativeDiscount = new PercentageDiscount(-10); // This will throw the exception
    $cart->setDiscount($negativeDiscount);  // This won't be reached
} catch (InvalidDiscountException $e) {
    echo "<b>".$e->getMessage()."</b>";  // Handle exception
}

// Display the Total price Basket after negative discount
echo "</br>Total price Basket after negative discount (no VAT): " . number_format($cart->calculateTotalPriceWithoutVat(), 2) . " " . $config->getCurrency() . "</br>";
echo "Total price Basket after negative discount (with VAT): " . number_format($cart->calculateTotalPriceWithVat(), 2) . " " . $config->getCurrency() . "</br>";

// Clear the cart
echo "</br>Clearing the cart...</br>";
$cart->clearCart();

// Display cart after clearing
echo "Products in the cart after clearing:</br>";
if (empty($cart->getProducts())) {
    echo "Cart is empty.</br>";
} else {
    displayCart($cart, $config);
}

// Helper function to display cart contents
function displayCart(Cart $cart, Config $config): void
{
    foreach ($cart->getProducts() as $product) {
        echo $product->getArticleNumber() . ': ' . $product->getDescription() . ' (' . $product->getQuantity() . ' units) - ' . $product->getPrice() . " " . $config->getCurrency() . " <i>(Total: " . ($product->getQuantity() * $product->getPrice()) . "</i> " . $config->getCurrency() . ")</br>";
    }

    echo "</br>Total price Basket (no VAT): " . number_format($cart->calculateTotalPriceWithoutVat(), 2) . " " . $config->getCurrency() . "</br>";
    echo "Total price Basket (with VAT " . $config->getVatRate() . "%): " . number_format($cart->calculateTotalPriceWithVat(), 2) . " " . $config->getCurrency() . "</br>";
}

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

