# Order & Payment API

## 🚀 Features

### Core Functionality
- **Order Management**: Create, read, update, delete orders with status tracking
- **Payment Processing**: Secure payment processing with multiple gateway support
- **Multi-Gateway Support**: Credit Card and PayPal integration (extensible)
- **JWT Authentication**: Secure API access with token-based authentication

### Architecture Highlights
- **Modular Design**: Laravel Modules package for clean code separation
- **Strategy Pattern**: Extensible payment gateway implementation
- **Repository Pattern**: Clean data access layer abstraction
- **DTO Pattern**: Structured data transfer between layers
- **Service Layer**: Business logic encapsulation
- **Custom Exception Handling**: Structured API error responses

---

## 🛠️ Technology Stack

- **PHP 8.4+** - Modern PHP with strict typing
- **Laravel 12** - Latest Laravel framework
- **Laravel Modules** - Modular architecture use this package (https://nwidart.com/laravel-modules/v6/introduction)
- **JWT Auth** - Token-based authentication
- **MySQL** - Database support
- **PHPUnit** - Comprehensive testing

---




### Authentication (`/api/auth`)
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/register` | Register new user |
| POST | `/api/auth/login` | Login and get JWT token |
| POST | `/api/auth/refresh-token` | Refresh JWT token |
| POST | `/api/auth/logout` | Logout (invalidate token) |

### Orders (`/api/orders`)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/orders` | List all orders (paginated) |
| POST | `/api/orders` | Create new order |
| GET | `/api/orders/{id}` | Get order details |
| PUT | `/api/orders/{id}` | Update order |
| DELETE | `/api/orders/{id}` | Delete order |

### Payments (`/api/payments`)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/payments` | List all payments |
| POST | `/api/payments/orders/{id}/pay` | Process payment for order |
| GET | `/api/payments/orders/{id}` | Get payments for specific order |
| GET | `/api/payments/orders/{id}/list` | List payments for order |

---

## 🔧 Setup Instructions

### Prerequisites
- PHP 8.4+
- Composer
- MySQL  

### Option 1: Local Development

```bash
# Clone the repository
git clone <repository-url>
cd order-payment-api

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# create new db called(ecommerce_db) to run migrations

# Run migrations
php artisan migrate

# Start development server
php artisan serve
```

### Running Tests

```bash
# Run all tests

php artisan test
```


---

## 💳 Payment Gateway Extensibility

### Adding a New Payment Gateway

**Step 1: Create the Gateway Class**

```php
// Modules/Payments/Gateways/StripeGateway.php
namespace Modules\Payments\Gateways;

use Modules\Orders\Models\Order;
use Modules\Payments\Enums\PaymentMethodEnum;
use Modules\Payments\Enums\PaymentStatusEnum;
use Modules\Payments\DTO\PaymentResultDTO;

class StripeGateway implements PaymentGatewayInterface
{
    public function pay(Order $order, array $data): PaymentResultDTO
    {
        // Stripe API integration here
        // Return PaymentResultDTO with payment details
        
        return new PaymentResultDTO(
            payment_id: 'stripe_' . uniqid(),
            status: PaymentStatusEnum::SUCCESSFUL,
            method: PaymentMethodEnum::STRIPE,
            metadata: $data
        );
    }
}
```

**Step 2: Add Enum Value**

```php
// Modules/Payments/Enums/PaymentMethodEnum.php
enum PaymentMethodEnum :int
{
    case CREDIT_CARD = 1;
    case PAYPAL = 2;
    case STRIPE = 3;  // Add new gateway
}
```

**Step 3: Update Factory**

```php
// Modules/Payments/Factories/PaymentGatewayFactory.php
class PaymentGatewayFactory
{
    public static function make(string $method): PaymentGatewayInterface
    {
        return match((int)$method) {
            PaymentMethodEnum::CREDIT_CARD->value => new CreditCardGateway(),
            PaymentMethodEnum::PAYPAL->value => new PayPalGateway(),
            PaymentMethodEnum::STRIPE->value => new StripeGateway(), // Add new gateway
            default => throw new InvalidArgumentException("Payment method {$method} not supported")
        };
    }
}
```

**No changes needed to:**
- `PayOrderService` - Business logic remains unchanged
- Controllers - Same API interface
- Frontend clients - Same request format
- Existing tests - Continue to work

---

## 🔒 Security Features

- **JWT Authentication**: Secure token-based API access
- **Request Validation**: Form request validation classes
- **SQL Injection Protection**: Laravel's built-in protection
- **Mass Assignment Protection**: `$guarded` properties on models
- **Rate Limiting**: Configured for auth endpoints
- **Ownership Validation**: `OwnedBy` scope ensures users access only their data
- **Payment Idempotency**: Prevents duplicate payments for same order

