IsOwnedBy-Behavior
==================

The IsOwnedBy-Behavior is used to check easily of an entity is owned by a specific user.

[doc_toc]

Loading
-------

You can load the behavior by the following code:

    public function initialize(array $config) {
        // code
        $this->addBehavior('Utils.IsOwnedBy');
    }

Configurations
--------------

Default, the behaviors watches the column `user_id` to check if the column contains the users user-id.
This can be changed with this code:

    public function initialize(array $config) {
        // code
        $this->addBehavior('Utils.IsOwnedBy', [
            'column' => 'created_by'
        ]);
    }

Now, the column `created_by` will be used.

IsOwndBy-method
---------------

Via the method `IsOwnedBy` you can validate if the entity belongs to an user. Look at this example:

    if($this->Articles($this->Articles->get(1), $user)) {
        // The article is owned by the user ($user)
    } else {
        // The article isn't owned by the user ($user)
    }

The first parameter can be an `Entity` or `array`. The second parameter is the user. Use `$this->Auth->user();` in your
controller to get the user.
