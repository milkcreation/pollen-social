# Social PresstiFy Plugin

[![Latest Version](https://img.shields.io/badge/release-2.0.34-blue?style=for-the-badge)](https://svn.tigreblanc.fr/presstify-plugins/social/tags/2.0.34)
[![MIT Licensed](https://img.shields.io/badge/license-MIT-green?style=for-the-badge)](LICENSE.md)

**Social** offers a large collection of social media channels.

## Installation

```bash
composer require presstify-plugins/social
```

## Setup

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
// @see /vendor/presstify-plugins/social/resources/config/social.php
return [
      //...

      // ...
];
```
