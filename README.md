# Discount Demo App

This is a **Laravel 12 demo application** showcasing integration of the [`pujanaik/user-discount`](https://github.com/pujav/user-discount) package.  
The package provides user-level discounts with assignment, revocation, eligibility checks, usage caps, and deterministic application logic.

---

## 🚀 Requirements
- PHP 8.2+
- Composer
- MySQL (or any DB supported by Laravel)
- Node.js (optional, for frontend build)


# User Discount Package Demo

## Steps

1. git clone https://github.com/yourname/discount-demo-app.git  
2. cd discount-demo-app  
3. composer install  
4. cp .env.example .env  
5. php artisan key:generate  
6. Configure database in `.env`  
7. php artisan migrate  
8. composer require pujanaik/user-discount:@dev  
9. php artisan vendor:publish --provider="PujaNaik\UserDiscount\UserDiscountServiceProvider"  
10. php artisan migrate  
11. php artisan db:seed --class=DiscountSeeder   (optional)  
12. php artisan serve  
13. Open [http://localhost:8000/discount-demo](http://localhost:8000/discount-demo)
14. Run tests  
    ```bash
    composer test
    ```
