# Ncloud SENS Alimtalk notifications channel for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/comento/laravel-sens-alimtalk?style=flat-square)](https://packagist.org/packages/comento/laravel-sens-alimtalk)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://travis-ci.org/comento/laravel-sens-alimtalk.svg?branch=master)](https://travis-ci.org/comento/laravel-sens-alimtalk)
[![Total Downloads](https://img.shields.io/packagist/dt/comento/laravel-sens-alimtalk.svg?style=flat-square)](https://packagist.org/packages/comento/laravel-sens-alimtalk)

This package makes it easy to send notifications using [Ncloud SENS Alimtalk](https://docs.ncloud.com/ko/sens/sens-1-5.html) with Laravel

## Contents

- [Installation](#installation)
    - [Setting up the Smsapi service](#setting-up-the-smsapi-service)
- [Usage](#usage)
    - [Available Message methods](#available-message-methods)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [License](#license)


## Installation

You can install the package via composer:

```bash
composer require comento/laravel-sens-alimtalk
```

You can also publish the config file with:

```bash
php artisan vendor:publish --provider="Comento\SensAlimtalk\SensAlimtalkServiceProvider"
```

### Setting up the Ncloud SENS Alimtalk service

Set your key and defaults in `config/sens-alimtalk.php`:

```php
/*
|--------------------------------------------------------------------------
| NAVER CLOUD PLATFORM API
|--------------------------------------------------------------------------
|
| Go to My Page > Manage Accoutn > Manage Auth Key
| You can use a previously created authentication key or create a new api authentication key.
|
*/
'access_key' => env('NCLOUD_ACCESS_KEY', ''),
'secret_key' => env('NCLOUD_SECRET_KEY', ''),

/*
 * Service ID issued when you add a project
 */
'serviceId' => '',

/*
 * KakaoTalk Channel ID ((Old) Plus Friend ID)
 */
'plus_friend_id' => '',
```

## Usage

You can use this channel in your `via()` method:

```php
// ...
use Comento\SensAlimtalk\SensAlimtalkChannel;
use Comento\SensAlimtalk\SensAlimtalkMessage;

class MentoringAdopt extends Notification
{
    use Queueable;

    private $mobiles;
    private $weblink_url;

    public function __construct($mobiles, $weblink_url)
    {
        $this->mobiles = $mobiles;
        $this->weblink_url = $weblink_url;
    }

    public function via($notifiable)
    {
        return [SensAlimtalkChannel::class];
    }

    public function toSensAlimtalk($notifiable)
    {
        return (new SensAlimtalkMessage())
            ->templateCode('adopt')
            ->to($this->mobiles)
            ->content('축하합니다!
현직자님의 답변이 채택되었습니다!
어떤 답변인지 확인하러 가볼까요?')
            ->button(['type' => 'WL', 'name' => '지금 보러가기', 'linkMobile' => $this->weblink_url, 'linkPc' => $this->weblink_url])
            ->utmSource('utm_source=crm-kakao&utm_medium=alimtalk&utm_campaign=mentoring-adopt&utm_term=지금 보러가기&utm_content=');
    }
}
```

### Available Message methods

* `templateCode(string)`
* `to(string|array)`
* `content(string)`
* `button(array)`
* `reserveTime(string)`
* `reserveAfterMinute(int)`
* `reserveAfterDay(int)`
* `variables(array)`
* `utmSource(string)`

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email tech@comento.kr instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
