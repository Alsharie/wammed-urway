# wammed-urway  (URWAY Payment Gateway)
laravel package for urway payment getway



You can publish using the following command

 `php artisan vendor:publish --provider="Wameed\UrwayPaymentGateway\UrwayServiceProvider"`

When published, the `config/urway.php` config file contains:



```php
return [
    'auth' => [
        'terminal_id' => env('URWAY_TERMINAL_ID'),
        'password' => env('URWAY_PASSWORD'),
        'merchant_key' => env('URWAY_MERCHANT_KEY'),
    ],
    'url'=>[
        //change 'payments-dev.urway-tech' to 'payments.urway-tech' when you are ready to go live
        'base'=>env('URWAY_BASE_URL','https://payments-dev.urway-tech.com'),
        'payment'=>env('URWAY_PAYMENT_URL','URWAYPGService/transaction/jsonProcess/JSONrequest'),
    ]
];
```
