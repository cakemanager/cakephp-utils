Metas-Behavior
==================

> Warning: This behavior is still in development and not stable!

The Metas-Behavior can be used to add custom meta-data to your entities without creating an extra column. 
This can be useful when you build things that are extendable (like plugins).

[doc_toc]

Loading
-------

You can load the behavior by the follwing code:

      public function initialize(array $config) {
          // code
          $this->addBehavior('Utils.Metas');
      }

Registering the Getters and Setters
--------------------

We made it as easy as possible for you to add meta-fields. Wouldn't it be great if you could treath your meta-fields just like the default fields of your table?
To do that you have to add `getters` and `setters` in your `Entity`.

        protected function _getCustomfield() {
            $model = TableRegistry::get('Bookmarks');
            return $model->registerGetter($this, 'customfield');
        }
      
        protected function _setCustomfield($value) {
            $model = TableRegistry::get('Bookmarks');
            $this->metas = $model->registerSetter($this, 'customfield', $value);
        }
        
Now you are able to get and set your `customfield`.

Some examples:

        // getting the customfield
        echo $entity->customfield;
        
        // form-field for the meta-field
        echo $this->Form->input('customfield');
        
        // setting the customfield
        $entity->customfield = "My Value";
        

Tips and Tricks
---------------

### For Api

If you use metafields for api's, you see the getters didn't work. This is because it works like [Virtual Fields](http://book.cakephp.org/3.0/en/orm/entities.html#exposing-virtual-properties).

By adding the fiels to the `$_virtual`-array they will be loaded with your entity:

      protected $_virtual = ['customfield'];
      
You can also add virtual fields at runtime:

      $entity->virtualProperties(['customfield']);

### Traits

If you're building a plugin and want to let the user decide to use meta-keys for his entity you can use traits.

Example:

      // Customfield/Model/Entity/CustomfieldTrait.php
      
      namespace Customfield\Model\Entity;
      
      trait CustomfieldTrait {
      
        protected function _getCustomfield() {
            $model = TableRegistry::get('Bookmarks');
            return $model->registerGetter($this, 'customfield');
        }
      
        protected function _setCustomfield($value) {
            $model = TableRegistry::get('Bookmarks');
            $this->metas = $model->registerSetter($this, 'customfield', $value);
        }
      
      }
      
Now we've created the trait, we are able to `use` the trait.

      namespace App\Model\Entity;
      
      use Cake\ORM\Entity;
      use Customfield\Model\Entity\CustomfieldTrait;
      
      class Article extends Entity
      {
          use CustomfieldTrait;
      }
