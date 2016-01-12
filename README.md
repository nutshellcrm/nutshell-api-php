This is some sample code which will help you get started using [Nutshell's API](http://nutshell.com/api) in your PHP application. This repository contains:

* a lightweight [JSON-RPC](http://json-rpc.org/) + [CURL](http://php.net/manual/en/book.curl.php) wrapper to ease making API calls and manage authentication and endpoint discovery
* several examples which use that class to perform certain tasks in Nutshell

## Running the Examples

Once you've cloned the repository, and assuming PHP is in your path:

1. set your Nutshell username and API key at the top of each PHP file in the `examples` directory
2. run `php examples/create.php` (or `edit.php` or `retrieve.php`)

## Using the API

The `NutshellApi` class (in `NutshellApi.php`) provides a lightweight [JSON-RPC](http://json-rpc.org/) + [CURL](http://php.net/manual/en/book.curl.php) wrapper for the API which you can use for development. The class handles authentication and endpoint discovery. It should be instantiated once with a username and API key and, once instantiated, it should be reused for subsequent API calls.

See **[the API method list](http://www.nutshell.com/api/detail/class_nut___api___core.html)** for a list of valid API methods.

Read the comments in `NutshellApi.php` for additional information about the class, and open the example files to see how the `NutshellApi` class is used.

The `NutshellApi` class provides synchronous (blocking) API calls. It is possible to write an API class which provides asynchronous calls using the `id` field specified in [JSON-RPC](http://groups.google.com/group/json-rpc/web/json-rpc-2-0).

## Composer

Thanks to the community [for nudging us to make this code compatible with Composer](https://github.com/nutshellcrm/nutshell-api-php/pull/5). While it's not meant to be a fully-supported SDK, you can use [composer](https://getcomposer.org/) to quickly add this project to your own.

## Additional Reading

* On API keys and permissions: [Authentication](http://www.nutshell.com/api/authentication.html)
* for `create.php`: [Entities and Relationships](http://www.nutshell.com/api/entities-relationships.html)
* for `edit.php`: [Retrieving and Editing Entities](http://www.nutshell.com/api/retrieving-editing.html)
* [API method list](http://www.nutshell.com/api/detail/class_nut___api___core.html)
* [All API Docs](http://www.nutshell.com/api/)

## About Nutshell

[Nutshell](http://nutshell.com) is building modern CRM software around the needs of medium-sized companies. We’re working hard to deliver the future of business software: enterprise-level features with the ease-of-use found in popular web applications.

At Nutshell, mobile is in our DNA. We believe in designing software to be cross-platform from day one, with robust APIs and native user interfaces. Our [iPhone](http://itunes.apple.com/us/app/nutshell/id337938121?mt=8) and [Android](https://play.google.com/store/apps/details?id=com.nutshell.crm) applications are built using this API.

The data you store in Nutshell belongs to you, and we've designed [our open API](http://nutshell.com/api) to make it possible for you to work with it however you wish. Please [contact us](http://www.nutshell.com/support/) with any questions on using the API.
