WhoDidIt-Behavior
==================

The WhoDidIt-Behavior can be used to know who created, and who modified a specific post. 

Loading
-------

You can load the behavior by the follwing code:

    public function initialize(array $config) {
        // code
        $this->addBehavior('CakeManager.WhoDidIt');
    }

Registering the data
--------------------

The behavior will use two fields: `created_by` and `modified_by`. Just create this fields in your database, and the behavior will add the user-id in the right field. You don't need to add this at you forms.

### Changing the columns
You can change the fields in the config of your behavior:

    public function initialize(array $config) {
        // code
        $this->addBehavior('CakeManager.WhoDidIt', [
            'created_by' => 'user_id',      // now the field 'user_id' will contain the user-id when created
            'modified_by' => false,         // the user who will modifie the post won't be registered
        ]);
    }

The behavior will get the users `id` from the Auth-data in the Session.

Getting the data
----------------    

The behavior will automatically get the user-data of the user who created and modified the post.

    $data = $this->Model->get(1);
        
    echo $data->created_by->id;     // echo's the user-id of the user who created the entity
    echo $data->modified_by->email; // echo's the email of the user who modified the entity

### Changing the Model
Maybe are you using another Model instead of the default Model (`CakeManager,Users`).
You can change the model in the config of your behavior:

    public function initialize(array $config) {
        // code
        $this->addBehavior('CakeManager.WhoDidIt', [
            'userModel' => 'my_custom_model',
        ]);
    }

### Setting the fields

Sometimes you don't need every data of the user, it makes your queries much slower. So, you are able to define the fields you want of an user. When leaving empty, it will return all columns.

    public function initialize(array $config) {
        // code
        $this->addBehavior('CakeManager.WhoDidIt', [
            'fields' => ['id', 'role_id', 'email'],
        ]);
    }
    
The example above will only return the users `id`, `role_id` and `email`.
