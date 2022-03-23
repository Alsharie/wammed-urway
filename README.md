# wammed-urway  (URWAY Payment Gateway)
laravel package for urway payment getway


install the package 
`composer install wameed/urway-payment-gateway`


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


send payment data

```php

  $urway = new Urway();

  $urway->setTrackId($trackID)
        ->setAmount($total_after_cal_tax)
        ->setCurrency('SAR')
        ->setCountry('SA')
        ->setAttribute('udf1', 'udf1')
        ->setPaymentPageLanguage('ar')
        ->setAttribute('udf4', 'udf4')
        ->setAttribute('udf5', 'udf5')
        ->setCustomerEmail($request->email)
        ->setRedirectUrl(route('user.payment.verify'));

  $response = $urway->pay();

  $payment_url = $response->getPaymentUrl();
```

to veriry the payment 

```php
        $urway = new Urway();

        $urway->setTrackId(request('TrackId'))
            ->setAmount(request('amount'))
            ->setCurrency('SAR');

        $redirect_url = $urway->verify(request('TranId'));

        return $redirect_url->body();

```
