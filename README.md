
[![Build Status](https://travis-ci.org/cakemanager/cakephp-utils.svg?branch=master)](https://travis-ci.org/cakemanager/cakephp-utils)
[![Coverage Status](https://coveralls.io/repos/cakemanager/cakephp-utils/badge.svg?branch=master)](https://coveralls.io/r/cakemanager/cakephp-utils?branch=master)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Latest Stable Version](https://poser.pugx.org/cakemanager/cakephp-utils/v/stable.svg)](https://packagist.org/packages/cakemanager/cakephp-utils) 
[![Total Downloads](https://poser.pugx.org/cakemanager/cakephp-utils/downloads.svg)](https://packagist.org/packages/cakemanager/cakephp-utils) 
[![License](https://poser.pugx.org/cakemanager/cakephp-utils/license.svg)](https://packagist.org/packages/cakemanager/cakephp-utils)

Utils Plugin for Cake 3.x
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

- [Authorizer](http://cakemanager.org/docs/utils/1.0/components/authorizer/) - Component to work easily with authorization in your application-controllers.
- [GlobalAuth](http://cakemanager.org/docs/utils/1.0/components/globalauth/) - Enables global access to user-data.
- [Menu](http://cakemanager.org/docs/utils/1.0/components/menu/) - Adds menu-items in sections to pass to your view.
- [Search](http://cakemanager.org/docs/utils/1.0/components/search/) - Creates filters for your queries so you are able to filter on your list with a generated form.

### Behaviors

- [Metas](http://cakemanager.org/docs/utils/1.0/behaviors/metas/) - Behavior to add meta-data to your current model.
- [Stateable](http://cakemanager.org/docs/utils/1.0/behaviors/stateable/) - Generates multiple states (like concept, deleted, active) and with save-method and finders.
- [Uploadable](http://cakemanager.org/docs/utils/1.0/behaviors/uploadable/) - Great behavior to upload files automatically.
- [WhoDidIt](http://cakemanager.org/docs/utils/1.0/behaviors/whodidit/) - Saves the user who created the row, and modified the row.
- [IsOwnedBy](http://cakemanager.org/docs/utils/1.0/behaviors/isownedby/) - Checks if your entity is owned by the given (logged in) user.

Documentation
-------------

For documentation, as well as tutorials, see the [documentation](http://cakemanager.org/docs/utils/1.0/) on [cakemanager.org](http://cakemanager.org).

Support
-------

- [CakeManager Website](http://cakemanager.org/) - Website of the CakeManager Team. Here you can find everything about us and our plugins.

- [Gitter](https://gitter.im/cakemanager/cakephp-utils) - Chat Tool for GitHub to talk about issues and new features.

- [GitHub](https://github.com/cakemanager/cakephp-utils/issues) - When there's something wrong, please open a new issue!

- [CakeManager Docs](http://cakemanager.org/docs/1.0/) - Documentation about the CakeManager Plugin.

- [CakePHP Utils Plugin Docs](http://cakemanager.org/docs/utils/1.0/) - Documentation about the Utils Plugin.


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
