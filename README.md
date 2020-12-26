# Social Component

[![Latest Version](https://img.shields.io/badge/release-1.0.0-blue?style=for-the-badge)](https://www.presstify.com/pollen-solutions/social/)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-green?style=for-the-badge)](LICENSE.md)

**Social** offers a large collection of social media channels.

## Installation

```bash
composer require pollen-solutions/social
```

## Pollen Framework Setup

### Declaration

```php
// config/app.php
return [
      //...
      'providers' => [
          //...
          \Pollen\Social\SocialServiceProvider::class,
          //...
      ];
      // ...
];
```

### Configuration

```php
// config/social.php
// @see /vendor/pollen-solutions/social/resources/config/social.php
return [
      //...

      // ...
];
```

