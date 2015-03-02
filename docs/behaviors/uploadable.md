Uploadable-Behavior
==================

With the Uploadable-behavior you are able to upload files easily.
Via this states you are able to save / change them, and get a list via the finders of CakePHP.

Loading
-------
You can load the behavior in your model via:

    $this->addBehavior('Utils.Uploadable', []);

Usage
-----

Imagine you want to make the feature an user is able to add an image as avatar.

First of all you need a new column in your table. Let's call it `avatar`.

Now you need to let our behavior know that you want to make the field `avatar` an upload-field. 
You have to do that when you add the behavior to the model:

    $this->addBehavior('Utils.Uploadable', [
        'avatar',
    ]);

Also we have to customize our view:

    // add the type to the create-method
    echo $this->Form->creat($entity, ['type' => 'file']);

    // add the avatar-input
    echo $this->Form->input('avatar', ['type' => 'field']);

Now we are done. you are ready to uplaod your file. In the next section all configurations under your field will be described.

Configurations
--------------
There are multiple configurations for the behavior available. Note that configurations will be done under a regsitered field.

Example:

The empty array of `avatar` will contain the configurations

### Fields
The `fields` configuration contains an array with avaliable fields:

- directory - this is the field who will contain the directory to your file.
- type - this field will contain the type of the uplaoded file.
- size - this field contains the size of the uploaded file.

Note that all of the fields will be default set to false, so not used. Only the `directory` field will be automatically set to the name of your given field.
So in the previous example it would be set to `avatar`.

Example:

    $this->addBehavior('Utils.Uploadable', [
        'avatar' => [
          'fields' => [
            'size' => 'avatar_size',
            'type' => 'avatar_type'
          ]
        ],
    ]);


### Remove File On Update

The `removeFileOnUpdate` configuration is a boolean you can set if you want to remove all previous files on a new uploaded file.
So imagine I will upload an avatar for the second time. The previous avatar would be deleted if you set `removeFileOnUpdate` to `true`.

Example:

    $this->addBehavior('Utils.Uploadable', [
      'avatar' => [
        'removeFileOnUpdate' => true
        ],
      ]
    ]);

> Note: When the uploaded file has the same name (and extension) as the previous file, it will be overridden.


### Remove File On Delete

The `removeFileOnDelete` configuration is a boolean you can set if you want to remove all files when delete the linked entity.
So, looking to the previous example: When I remove my account, my avatar(s) would be deleted when `removeFileOnDelete` is set to `true`.

Example:

    $this->addBehavior('Utils.Uploadable', [
      'avatar' => [
        'removeFileOnDelete' => true
        ],
      ]
    ]);
    

### Path

The `path` configuration contains the path to write the file to. Default this configuration looks like:

    ```
    {ROOT}{DS}{WEBROOT}{DS}uploads{DS}{model}{DS}{field}{DS}
    ```

This path would be converted to: `webroot/uploads/Users/5/` (asuming our user-id is `5`.

The following templates are avaiable:

- {ROOT}
- {WEBROOT}
- {field}
- {model}
- {DS}
- //
- /
- \\

To Do
-----

The following features will be added soon:

- Delete files on delete of entity
- Validation methods
- Tests
