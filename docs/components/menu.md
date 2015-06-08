Menu-Component
==============

The Menu-Component allows us to create dynamic menu's for multiple areas.

[doc_toc]

Loading
--------

You can load the component in your `AppController`:

    public function initialize() {
        // code
        $this->loadComponent('Utils.Menu');
    }
    
Configuring
-----------

There are no configurations yet...


Usage
-----

### Areas
A web-page can contain multiple areas for menu's. Think about a main-area, a header, and a footer.
The menu-items for the Menu-Component are stored in a specific area.

An area can be set via: 

    $this->Menu->area('MyArea');

To get the current area (string) you can use:

    $this->Menu->area();

    
### Adding items
An example of adding an item:

    $this->Menu->add('Bookmarks', [
            'id'  => 'App.Bookmarks',
            'url' => [
                'plugin'     => false,
                'prefix'     => false,
                'controller' => 'bookmarks',
                'action'     => 'index'
            ]
    ]);

The first parameter is default the title of the item.

The second parameter is an array of settings. 

- id            - You can add an id to your item. This can be usefull for controlling the item, like editing, or deleting it's item.
- url           - Adding an url is just like CakePHP: adding an array or string.
- parent        - Not supported yet... This can be used for adding items under items.
- title         - The title can also be set via this setting. This is needed when you use the first parameter as ID.
- weight        - This is a value to order your items. If you add a specific item after another, but give it a lower integer (weight is default `10`), it will be sorted above the other item.
- icon          - Sometimes you want to add icons (like font-awesome). You can add icons via this setting.
- area          - Changing the area can also via this setting.
- active        - This setting is default set to false; and will automatically be set to true if the given url is equal to the current url. The helpers will be able to read this and react on it.

Note: Sub-items (adding a parent) is not supported yet...

### Removing items
You can remove items using it's id:

    $this->Menu->remove('Bookmarks');


### Getting items
You can get the current menu-list by the `getMenu`-method:

    // get the whole menu with all areas
    $this->Menu->getMenu();

    // get specific area
    $this->Menu->getMenu('main');

### Activate items
By using the `active`-method, you can make the used id active:

    // makes the 'blogs' item active
    $this->Menu->active('blogs');
    
### Controller
Adding menu-items can be done in your `AppController`. For that, the `MenuComponent` calls an `initMenuItems`-method. So, when you add 
the following to your `AppController`, menu-items will be added:

    public function initMenuItems() {
        
        // your items here

    }

Using the menu-data
-------------------

The component creates an variable `$menu` for your view. This variable is an array with the area's as keys.
If you select a key, you have an array of all items with the ID as key and options as data.

### Loading a helper


### Showing menu's
With the following code you are able to generate a menu-list for the chosen area:

    // the menu is generated for the area `main` and the helper `MainMenu` is used.
    $this->Menu->menu('main', 'MainMenu');

`MainMenu` is an helper wich is a template for the menu.

### Custom menu templates
Of course you want to create your own menu templates. For that, we added the `MenuBuilderInterface`. You can easily use
the interface this way:


    namespace App\View\Helper;
    
    use Cake\View\Helper;
    use Utils\View\Helper\MenuBuilderInterface;
    
    class MainMenuHelper extends Helper implements MenuBuilderInterface
    {
        /**
         * Used helpers
         *
         * @var array
         */
        public $helpers = [
            'Html'
        ];
    
        /**
         * Default configuration.
         *
         * @var array
         */
        protected $_defaultConfig = [];
    
        public function afterMenu($menu = [], $options = []) { return ''; }
        
        public function afterSubItem($item = [], $options = []) { return ''; }
    
        public function beforeMenu($menu = [], $options = []) { return ''; }
    
        public function beforeSubItem($item = [], $options = []) { return ''; }
    
        public function item($item = [], $options = []) { return ''; }
    
        public function subItem($item = [], $options = []) { return ''; }
    }

The interface `MenuBuilderInterface contains the following helper-methods:

### beforeMenu
Used to start a menu.

### item
An item itself.

### subItem
An subItem itself.

### beforeSubItem
Used to start a subItem.

### afterSubItem
Used after an subItem.

#### afterMenu
Used when all menu items are viewed, and the menu will be closed.


All methods contain the attribute `$item`, with the current menuItem, and should return a string wich contains html.
If a method is unused, let it return an empty string.

