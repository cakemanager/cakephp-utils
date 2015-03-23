Utils Plugin for Cake 3.x
===================

The Utils plugin offers you many components and behaviors to make developing easier. This plugin is required by the [CakeManager plugin](http://github.com/cakemanager/cakephp-cakemanager), but it's good in use as individual plugin!

Usage
-----

Run composer: `composer require cakemanager/cakephp-utils`

In `app/Config/bootstrap.php` add: `Plugin::load('Utils');`

### Components

- [Authorizer](http://cakemanager.org/docs/utils/1.0/components/authorizer/) - Component to work easily with authorization in your application-controllers.
- [Menu](http://cakemanager.org/docs/utils/1.0/components/menu/) - Adds menu-items in sections to pass to your view.
- [Search](http://cakemanager.org/docs/utils/1.0/components/search/) - Creates filters for your queries so you are able to filter on your list with a generated form.

### Behaviors

- [Metas](http://cakemanager.org/docs/utils/1.0/behaviors/metas/) - Behavior to add meta-data to your current model.
- [Stateable](http://cakemanager.org/docs/utils/1.0/behaviors/stateable/) - Generates multiple states (like concept, deleted, active) and with save-method and finders.
- [Uploadable](http://cakemanager.org/docs/utils/1.0/behaviors/uploadable/) - Great behavior to upload files automatically.
- [WhoDidIt](http://cakemanager.org/docs/utils/1.0/behaviors/whodidit/) - Saves the user who created the row, and modified the row.

Documentation
-------------

For documentation, as well as tutorials, see the [documentation](http://cakemanager.org/docs/utils/1.0/) on [cakemanager.org](http://cakemanager.org).

Support
-------

For bugs and feature requests, please use the [issues](https://github.com/cakemanager/cakephp-utils/issues) section of this repository.

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
