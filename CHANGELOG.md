# Changelog

All Notable changes to `sens-alimtalk` will be documented in this file

## 8.0.0 - 2026-09-22

### Added

- Support Laravel 13 and PHPUnit 13
- Test coverage for message payload building, request signing and the notification channel (17 tests). The previous suite contained no executable test.

### Breaking changes

- Raise the minimum PHP version to 8.1. PHP 7.x has been end-of-life since 2022 and was not covered by any test.
- Require `guzzlehttp/guzzle` `^7.15.2`. Guzzle 6 is dropped because it has no release fixing CVE-2026-69245 / CVE-2026-69246.

### Fixed

- `linkMobile()` and `linkPc()` no longer create dynamic properties, which are deprecated as of PHP 8.2 and removed in PHP 9.

### Changed

- Migrate `phpunit.xml.dist` to the PHPUnit 10+ schema.
- Run CI on PHP 8.1 through 8.5 and drop the removed `--verbose` flag.

## 7.0.0 - 2025-04-17

- Support Laravel 12 and PHPUnit 12

## 6.1.1 - 2024-10-08

- Default `useSmsFailover` to `true` when `sens-alimtalk.use_sms_failover` is not configured

## 6.1.0 - 2024-10-08

- Add `useSmsFailover()` method and the `use_sms_failover` config entry

## 6.0.0 - 2024-05-09

- Support Laravel 11 and PHPUnit 11

## 5.0.0 - 2023-11-28

- Support Laravel 10 and PHPUnit 10
- Fix a deprecation on PHP 8.2

## 4.2.2 - 2023-10-20

- Generate the request timestamp when the message is sent

## 4.2.1 - 2023-01-30

- Fix `Call to undefined method stdClass::routeNotificationFor()`

## 4.2.0 - 2022-11-25

- You can use `routeNotificationForSensAlimtalk` method on the notifiable entity

## 4.1.0 - 2022-10-12

- Add `countryCode()` method

## 4.0.0 - 2022-10-11

- Support Laravel 9

## 3.3.0 - 2022-07-21

- Add `setPlusFriendId()` method to override the configured KakaoTalk channel id
- Switch CI from Travis to GitHub Actions and require PHPUnit 9

## 3.2.0 - 2022-03-18

- Allow a custom SMS failover content

## 3.1.0 - 2021-05-11

- Fix a missing `Carbon` import

## 3.0.0 - 2021-04-15

- Support PHP8

## 2.0.0 - 2020-10-07

- Support Laravel 8.0

## 1.0.0 - 2020-06-10

- initial release
