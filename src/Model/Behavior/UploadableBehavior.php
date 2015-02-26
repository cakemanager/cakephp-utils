<?php

namespace Utils\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * Uploadable behavior
 */
class UploadableBehavior extends Behavior
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'defaultFieldConfig' => [
            'fields'             => [
                'directory' => false,
                'type'      => false,
                'size'      => false,
            ],
            'removeFileOnUpdate' => false,
            'removeFileOnDelete' => true,
            'field'              => 'id',
            'path'               => '{ROOT}{DS}{WEBROOT}{DS}uploads{DS}{model}{DS}{field}{DS}',
            'fileName'           => '{ORIGINAL}',
        ]
    ];

    /**
     * Preset cofiguration-keys who will be ignored by getting the fields
     *
     * @var type
     */
    protected $_presetConfigKeys = [
        'defaultFieldConfig',
    ];

    /**
     * Holder for the Table-Model
     *
     * @var type
     */
    protected $_Table = null;

    /**
     * Testmethod
     */
    public function test()
    {

        $this->normalizeAll();
    }

    /**
     * BeforeSave Callback
     *
     * @param type $event
     * @param type $entity
     * @param type $options
     */
    public function beforeSave($event, $entity, $options)
    {
        $this->_Table = $event->subject();
    }

    /**
     * AfterSave Callback
     *
     * @param type $event
     * @param type $entity
     * @param type $options
     */
    public function afterSave($event, $entity, $options)
    {
        $fields = $this->getFieldList();

        foreach ($fields as $field => $data) {

            if ($this->_ifUploaded($entity, $field)) {

                if ($this->_uploadFile($entity, $field)) {
                    $this->_Table->save($this->_setUploadColumns($entity, $field));
                }
            }
        }
    }

    /**
     * Returns a list of all registered fields to upload
     *
     * ### Options
     * - normalize      boolean if each field should be normalized. Default set to true
     *
     * @param type $options
     */
    public function getFieldList($options = [])
    {

        $_options = [
            'normalize' => true,
        ];

        $options = Hash::merge($_options, $options);

        $list = [];

        foreach ($this->config() as $key => $value) {

            if (!in_array($key, $this->_presetConfigKeys) || is_integer($key)) {

                if (is_integer($key)) {
                    $field = $value;
                } else {
                    $field = $key;
                }

                if ($options['normalize']) {
                    $field_config = $this->_normalizeField($field);
                } else {
                    $field_config = (($this->config($field) == null) ? [] : $this->config($field));
                }

                $list[$field] = $field_config;
            }
        }

        return $list;
    }

    /**
     * Method to normalize all fields
     */
    public function normalizeAll()
    {

        $this->getFieldList();
    }

    /**
     * Normalizes the requested field
     *
     * ### Options
     * - save           boolean if the normalized data should be saved in config
     *                  default set to true
     *
     * @param string $field
     */
    protected function _normalizeField($field, $options = [])
    {

        $_options = [
            'save' => true,
        ];

        $options = Hash::merge($_options, $options);

        $data = $this->config($field);

        if (is_null($data)) {

            foreach ($this->config() as $key => $config) {
                if ($config == $field) {

                    if ($options['save']) {
                        $this->config($field, []);

                        $this->_configDelete($key);
                    }

                    $data = [];
                }
            }
        }

        // adding the default directory-field if not set
        if (is_null(Hash::get($data, 'fields.directory'))) {
            $data = Hash::insert($data, 'fields.directory', $field);
        }

        $data = Hash::merge($this->config('defaultFieldConfig'), $data);

        if ($options['save']) {
            $this->config($field, $data);
        }

        return $data;
    }

    /**
     * Checks if an file has been uploaded by user.
     *
     * Returns boolean
     *
     * @param type $entity
     * @param type $field
     * @return boolean
     */
    protected function _ifUploaded($entity, $field)
    {
        if ($entity->get($field)) {
            $data = $entity->get($field);

            if (!empty($data['tmp_name'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Uploads the file to the directory
     *
     * @param type $entity
     * @param type $field
     * @param type $options
     */
    protected function _uploadFile($entity, $field, $options = [])
    {

        $field_config = $this->config($field);

        $_upload = $entity->get($field);

        $upload_path = $this->_getPath($entity, $field, ['file' => true]);

        // creating the path if not exists
        if (!file_exists($this->_getPath($entity, $field, ['file' => false]))) {
            mkdir($this->_getPath($entity, $field, ['file' => false]), 0777, true);
        }

        // upload the file and return true
        if (move_uploaded_file($_upload['tmp_name'], $upload_path)) {
            return true;
        }

        return false;
    }

    /**
     * Writes all data of the uplaod to the entity
     *
     * Returns the modified entity
     *
     * @param type $entity
     * @param type $field
     * @param type $options
     * @return Entity who is modified
     */
    protected function _setUploadColumns($entity, $field, $options = [])
    {

        $field_config = $this->config($field);

        $_upload = $entity->get($field);

        // set all columns with values
        foreach ($field_config['fields'] as $key => $column) {
            if ($column) {
                if ($key == "directory") {
                    $entity->set($column, $this->_getPath($entity, $field, ['root' => false, 'file' => true]));
                }
                if ($key == "type") {
                    $entity->set($column, $_upload['type']);
                }
                if ($key == "size") {
                    $entity->set($column, $_upload['size']);
                }
            }
        }

        return $entity;
    }

    /**
     * Returns te path of the given field
     *
     * @param Entity $entity
     * @param string $field
     * @param array $options
     */
    protected function _getPath($entity, $field, $options = [])
    {

        $_options = [
            'root' => true,
            'file' => false,
        ];

        $options = Hash::merge($_options, $options);

        $config = $this->config($field);

        $path = $config['path'];

        $replacements = array(
            '{ROOT}'    => ROOT,
            '{WEBROOT}' => 'webroot',
            '{field}'   => $entity->get($config['field']),
            '{model}'   => Inflector::underscore($this->_Table->alias()),
            '{DS}'      => DIRECTORY_SEPARATOR,
            '//'        => DIRECTORY_SEPARATOR,
            '/'         => DIRECTORY_SEPARATOR,
            '\\'        => DIRECTORY_SEPARATOR,
        );

        $builtPath = str_replace(array_keys($replacements), array_values($replacements), $path);

        if (!$options['root']) {
            $builtPath = str_replace(ROOT . DS . 'webroot' . DS, '', $builtPath);
        }

        if ($options['file']) {
            $builtPath = $builtPath . $entity[$field]['name'];
        }

        return $builtPath;
    }

    /**
     * Returns the fileName of the given field
     *
     * @param type $entity
     * @param type $field
     * @param type $options
     * @return type
     */
    protected function _getFileName($entity, $field, $options = [])
    {
        $_options = [
        ];

        $options = Hash::merge($_options, $options);

        $config = $this->config($field);

        $_upload = $entity->get($field);

        $fileName = $config['fileName'];

        $replacements = array(
            '{ORIGINAL}'  => $_upload['name'],
            '{field}'     => $entity->get($config['field']),
            '{extension}' => end(explode('.', $_upload['name'])),
            '{DS}'        => DIRECTORY_SEPARATOR,
            '//'          => DIRECTORY_SEPARATOR,
            '/'           => DIRECTORY_SEPARATOR,
            '\\'          => DIRECTORY_SEPARATOR,
        );

        $builtFileName = str_replace(array_keys($replacements), array_values($replacements), $fileName);

        return $builtFileName;
    }

}
