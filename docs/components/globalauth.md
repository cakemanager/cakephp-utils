GlobalAuth-Component
==============

Since CakePHP 3.0 it's very hard to get the logged in user into your models, behaviors and other classes beside
controllers (`$this->Auth->user()`).
The GlobalAuth-Component allows us to get the logged in user global.

[doc_toc]

Loading
--------

You can load the component in your `AppController`:

    public function initialize() {
        // code
        $this->loadComponent('Utils.GlobalAuth');
    }
    
Usage
-----

Whenever you need data of the logged in user use the following code:

    $user = Configure::read('GlobalAuth');
    
`$user` will now contain an array with the data of the user.
