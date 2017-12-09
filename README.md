# PRZELEWY24 for Laravel #

If you are looking for function reach, secure and easy to use library for your Laravel powered application. You are in the right place. 

Library provides support for communication with Przelewy24 via [API](https://przelewy24.pl/storage/app/media/pobierz/Instalacja/przelewy24_dokumentacja_3.2.pdf) and [WebServices](https://przelewy24.pl/storage/app/media/pobierz/Instalacja/przelewy24_webservices.pdf).

__Please note:__
This library is still in early stage of development. Use on your own risk.

### Requirements ###
* PHP 5.6+
* Laravel 5.4+
* PHP modules: php-soap
* optionally [netborgteam/laravel-slack](https://packagist.org/packages/netborgteam/laravel-slack) (for getting notification about payment events on Slack's channel)

### Installation ###
```
composer require netborg/laravel-przelewy24
composer update
```

Add Service Provider to your `config/app.php` file:
```php
		/*
         * Package Service Providers...
         */
        
        // ....
        NetborgTeam\P24\Providers\P24Provider::class,
```

Manually copy `config/p24.php` config file to your `config` directory or execute:
```
php artisan vendor:publish
```
Provide your Merchant details in your `.env` config file:
```
P24_MERCHANT_ID=			// your MerchantId received from Przelewy24 ie. `123456`
P24_POS_ID=					// your PosId received from Przelewy24 (or copy your MerchantId from above)
P24_CRC=					// your CRC available in your panel on Przelewy24.
P24_API_KEY=				// your API KEY available in panel on Przelewy24.
P24_MODE=sandbox			// switch between test and production modes: `live` or `sandbox`
```
