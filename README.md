# CodeIgniter 4 Application Starter

## What is CodeIgniter?

CodeIgniter is a PHP full-stack web framework that is light, fast, flexible and secure.
More information can be found at the [official site](https://codeigniter.com).

This repository holds a composer-installable app starter.
It has been built from the
[development repository](https://github.com/codeigniter4/CodeIgniter4).

More information about the plans for version 4 can be found in [CodeIgniter 4 on the forum](https://forum.codeigniter.com/forum-28.html)
and the [documentation](https://codeigniter.com/user_guide/index.html).

## Installation & Updates

`composer create-project codeigniter4/appstarter` then `composer update` whenever
there is a new release of the framework.

When updating, check the release notes to see if there are any changes you might need to apply
to your `app` folder. The `app` folder is yours to customize, and the framework won't overwrite it
unless you use the `spark` commands that do so.

## Setup

Copy `env` to `.env` and tailor for your app, specifically the baseURL
and any database settings.

## Important Change with index.php

`index.php` is no longer in the root of the project! It has been moved inside the `public` folder,
for better security and separation of concerns.

**This means you must configure your web server to point to the `public` folder instead of the project root.**

Please read the user guide for a better explanation of how CI4 works!

## Server Requirements

PHP version 7.4 or higher is required, with the following extensions installed:
- [intl](http://php.net/manual/en/intl.requirements.php)
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library
- [mbstring](http://php.net/manual/en/mbstring.installation.php)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [xml](http://php.net/manual/en/xml.requirements.php) (for XML support)
- [zip](http://php.net/manual/en/zip.requirements.php) (if you plan to use the Zip library)

Additionally, make sure that the following extensions are enabled in your PHP:
- json (enabled by default - don't turn it off)
- [mbstring](http://php.net/manual/en/mbstring.installation.php) (enabled by default - don't turn it off)
- [xml](http://php.net/manual/en/xml.requirements.php) (enabled by default - don't turn it off)

