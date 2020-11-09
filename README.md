
[![Build Status](https://travis-ci.org/cakemanager/cakephp-utils.svg?branch=master)](https://travis-ci.org/cakemanager/cakephp-utils)
[![Coverage Status](https://coveralls.io/repos/cakemanager/cakephp-utils/badge.svg?branch=master)](https://coveralls.io/r/cakemanager/cakephp-utils?branch=master)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Latest Stable Version](https://poser.pugx.org/cakemanager/cakephp-utils/v/stable.svg)](https://packagist.org/packages/cakemanager/cakephp-utils) 
[![Total Downloads](https://poser.pugx.org/cakemanager/cakephp-utils/downloads.svg)](https://packagist.org/packages/cakemanager/cakephp-utils) 
[![License](https://poser.pugx.org/cakemanager/cakephp-utils/license.svg)](https://packagist.org/packages/cakemanager/cakephp-utils)

Utils Plugin for Cake 4.x
=========================

The Utils plugin offers you many components and behaviors to make developing easier. This plugin is required by the [Cak

Installation
------------

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require cakemanager/cakephp-utils:dev-master
```

## Configuration

You will need to add the following line to your application's bootstrap.php file:

```php
Plugin::load('Utils');
```

Usage
-----

### Components

- [Authorizer](https://github.com/cakemanager/cakephp-utils/wiki/authorizer) - Component to work easily with authorization in your application-controllers.
- [GlobalAuth](https://github.com/cakemanager/cakephp-utils/wiki/globalauth/) - Enables global access to user-data.
- [Menu](https://github.com/cakemanager/cakephp-utils/wiki/menu/) - Adds menu-items in sections to pass to your view.
- [Search](https://github.com/cakemanager/cakephp-utils/wiki/search/) - Creates filters for your queries so you are able to filter on your list with a generated form.

### Behaviors

- [Metas](https://github.com/cakemanager/cakephp-utils/wiki/metas/) - Behavior to add meta-data to your current model.
- [Stateable](https://github.com/cakemanager/cakephp-utils/wiki/stateable/) - Generates multiple states (like concept, deleted, active) and with save-method and finders.
- [Uploadable](https://github.com/cakemanager/cakephp-utils/wiki/uploadable/) - Great behavior to upload files automatically.
- [WhoDidIt](https://github.com/cakemanager/cakephp-utils/wiki/whodidit/) - Saves the user who created the row, and modified the row.
- [IsOwnedBy](https://github.com/cakemanager/cakephp-utils/wiki/isownedby/) - Checks if your entity is owned by the given (logged in) user.


Support
-------

- [Gitter](https://gitter.im/cakemanager/cakephp-utils) - Chat Tool for GitHub to talk about issues and new features.

- [GitHub](https://github.com/cakemanager/cakephp-utils/issues) - When there's something wrong, please open a new issue!

- [CakePHP Utils Plugin Docs](https://github.com/cakemanager/cakephp-utils/wiki/) - Documentation about the Utils Plugin.


Contributing
------------

If you have a good idea for a new feature, feel free to pull or open a new  [issue](https://github.com/cakemanager/cakephp-utils/issues). Pull requests are always more than welcome!

License
-------

The MIT License (MIT)

Copyright (c) 2014 CakeManager by bobmulder

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
