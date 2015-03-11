Stateable-Behavior
==================

With the Stateable-behavior you are able to sort your data in multiple states. 
Via this states you are able to save / change them, and get a list via the finders of CakePHP.

[doc_toc]

Loading
-------
You can load the behavior in your model via:

    $this->addBehavior('Utils.Stateable', []);


Configurations
--------------
There are multiple configurations for the behavior. They will be explained.

### Field

The field is the field where the state should be saved. Default set to `state`.

    $this->Stateable->config('field', 'status');
    // our 'state' will now be saved under the field 'status'

### States

This is an array of the available states. The key of the array is the name of the state, the value of the array is the integer of the state. This value will be saved in your table

The default states are:

      'states' => [
          'concept' => 0,
          'active'  => 1,
          'deleted' => -1,
      ],
  
So, if your item has been saved with the state `active`, the integer `1` will be stored in your table.

Change the states by: 

      $this->Stateable->config('states', [
        'default' => 2,
      ]);

This code will expand the known states-array.

> Note: If you define your own states, don't forget to create your own finders!

Writing the data
----------------

### Forms
If you want to add states in a form, you have to add the following in your controller:

      // action
      $states = $this->Bookmarks->stateList();
      $this->set(compact('states'));
 
The `stateList()`-method flips the states-array of the behavior. So, if you add the following to your view:

    echo $this->Form->input('state');

The form will create a drop-down with the available states.

### Method

> Note: A method to change the current state is not available yet.

Getting the data
----------------
The states can be loaded via the [finders](http://book.cakephp.org/3.0/en/orm/retrieving-data-and-resultsets.html#using-finders-to-load-data) of CakePHP.
The Stateable-behavior has the following finders:

###  Concept
This finder gets all the concepts (0) of your table:

    $query = $articles->find('concept');

###  Active
This finder gets all the active rows (1) of your table:

    $query = $articles->find('active');
    
###  Deleted
This finder gets all the (soft)deleted rows (-1) of your table:

    $query = $articles->find('deleted');
