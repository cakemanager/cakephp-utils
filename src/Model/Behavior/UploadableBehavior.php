<?php
/**
 * CakeManager (http://cakemanager.org)
 * Copyright (c) http://cakemanager.org
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) http://cakemanager.org
 * @link          http://cakemanager.org CakeManager Project
 * @since         1.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Utils\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * Uploadable behavior
 *
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
            'fields' => [
                'directory' => false,
                'type' => false,
                'size' => false,
            ],
            'removeFileOnUpdate' => false,
            'removeFileOnDelete' => true,
            'field' => 'id',
            'path' => '{ROOT}{DS}{WEBROOT}{DS}uploads{DS}{model}{DS}{field}{DS}',
            'fileName' => '{ORIGINAL}',
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
    protected $_savedFields = [];

    /**
     * Holder for the Table-Model
     *
     * @var type
     */
    protected $_Table = null;

    /**
     * BeforeSave Callback
     *
     * @param \Cake\Event\Event $event Event.
     * @param \Cake\ORM\Entity $entity The Entity who will be saved.
     * @param array $options Options.
     * @return void
     */
    public function beforeSave($event, $entity, $options)
    {
        $this->_Table = $event->subject();
    }

    /**
     * AfterSave Callback
     *
     * @param \Cake\Event\Event $event Event.
     * @param \Cake\ORM\Entity $entity The Entity who has been saved.
     * @param array $options Options.
     * @return void
     */
    public function afterSave($event, $entity, $options)
    {
        $fields = $this->getFieldList();

        foreach ($fields as $field => $data) {
            if ($this->_ifUploaded($entity, $field)) {
                if ($this->_uploadFile($entity, $field)) {
                    if (!key_exists($field, $this->_savedFields)) {
                        $this->_savedFields[$field] = true;
                        $event->subject()->save($this->_setUploadColumns($entity, $field));
                    }
                }
            }
        }

        $this->_savedFields = null;
    }

    /**
     * Returns a list of all registered fields to upload
     *
     * ### Options
     * - normalize      boolean if each field should be normalized. Default set to true
     *
     * @param array $options Options.
     * @return array
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
                    $fieldConfig = $this->_normalizeField($field);
                } else {
                    $fieldConfig = (($this->config($field) == null) ? [] : $this->config($field));
                }

                $list[$field] = $fieldConfig;
            }
        }

        return $list;
    }

    /**
     * normalizeAll
     *
     * Method to normalize all fields.
     *
     * @return void
     */
    public function normalizeAll()
    {
        $this->getFieldList();
    }

    /**
     * _normalizeField
     *
     * Normalizes the requested field.
     *
     * ### Options
     * - save           boolean if the normalized data should be saved in config
     *                  default set to true
     *
     * @param string $field Field to normalize.
     * @param array $options Options.
     * @return array
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
     * _ifUploaded
     *
     * Checks if an file has been uploaded by user.
     *
     * @param \Cake\ORM\Entity $entity Entity to check on.
     * @param string $field Field to check on.
     * @return bool
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
     * _uploadFile
     *
     * Uploads the file to the directory
     *
     * @param \Cake\ORM\Entity $entity Entity to upload from.
     * @param string $field Field to use.
     * @param array $options Options.
     * @return bool
     */
    protected function _uploadFile($entity, $field, $options = [])
    {
        $_upload = $entity->get($field);
        $uploadPath = $this->_getPath($entity, $field, ['file' => false]);
        
        // creating the path if not exists
        if (!is_dir($this->_getDir($entity, $field, ['file' => false]))) {
            $this->_mkdir($this->_getDir($entity, $field, ['file' => false]), 0777, true);
        }

        // upload the file and return true
        if ($this->_moveUploadedFile($_upload['tmp_name'], $uploadPath)) {
            return true;
        }

        return false;
    }

    /**
     * _setUploadColumns
     *
     * Writes all data of the uplaod to the entity
     *
     * Returns the modified entity
     *
     * @param \Cake\ORM\Entity $entity Entity to check on.
     * @param string $field Field to check on.
     * @param array $options Options.
     * @return \Cake\ORM\Entity
     */
    protected function _setUploadColumns($entity, $field, $options = [])
    {
        $fieldConfig = $this->config($field);

        $_upload = $entity->get($field);

        // set all columns with values
        foreach ($fieldConfig['fields'] as $key => $column) {
            if ($column) {
                if ($key == "directory") {
                    if ($fieldConfig['removeFileOnUpdate']) {
                        @unlink(ROOT . DS . 'webroot' . DS.$entity->get($column));
                    }
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
     * _getDir
     *
     * Returns the folder path where the file must be uploaded
     *
     * @param \Cake\ORM\Entity $entity Entity to check on.
     * @param string $field Field to check on.
     * @param array $options Options.
     * @return string
     */
    protected function _getDir($entity, $field, $options = [])
    {
        $_options = [
            'root' => true,
            'file' => false,
        ];

        $options = Hash::merge($_options, $options);

        $config = $this->config($field);

        $path = $config['path'];

        $replacements = [
            '{ROOT}' => ROOT,
            '{WEBROOT}' => 'webroot',
            '{field}' => $entity->get($config['field']),
            '{model}' => Inflector::underscore($this->_Table->alias()),
            '{DS}' => DIRECTORY_SEPARATOR,
            '//' => DIRECTORY_SEPARATOR,
            '/' => DIRECTORY_SEPARATOR,
            '\\' => DIRECTORY_SEPARATOR,
        ];

        $builtPath = str_replace(array_keys($replacements), array_values($replacements), $path);

        return $builtPath;
    }

    /**
     * _getPath
     *
     * Returns te path of the given field.
     *
     * @param \Cake\ORM\Entity $entity Entity to check on.
     * @param string $field Field to check on.
     * @param array $options Options.
     * @return string
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

        $replacements = [
            '{ROOT}' => ROOT,
            '{WEBROOT}' => 'webroot',
            '{field}' => $entity->get($config['field']),
            '{model}' => Inflector::underscore($this->_Table->alias()),
            '{DS}' => DIRECTORY_SEPARATOR,
            '//' => DIRECTORY_SEPARATOR,
            '/' => DIRECTORY_SEPARATOR,
            '\\' => DIRECTORY_SEPARATOR,
        ];

        $builtPath = str_replace(array_keys($replacements), array_values($replacements), $path);

        if (!$options['root']) {
            $builtPath = str_replace(ROOT . DS . 'webroot' . DS, '', $builtPath);
        }

        if ($options['file']) {
            $builtPath = $builtPath . $entity[$field]['name'];
        } else {
            $builtPath = $builtPath . $this->_getFileName($entity, $field);
        }

        return $builtPath;
    }

    /**
     * _getFileName
     *
     * Returns the fileName of the given field.
     *
     * @param \Cake\ORM\Entity $entity Entity to check on.
     * @param string $field Field to check on.
     * @param array $options Options.
     * @return string
     */
    protected function _getFileName($entity, $field, $options = [])
    {
        $_options = [
        ];

        $options = Hash::merge($_options, $options);

        $config = $this->config($field);

        $_upload = $entity->get($field);

        $fileInfo = explode('.', $_upload['name']);
        $extension = end($fileInfo);

        $fileName = $config['fileName'];

        $replacements = [
            '{ORIGINAL}' => $_upload['name'],
            '{field}' => $entity->get($config['field']),
            '{extension}' => $extension,
            '{DS}' => DIRECTORY_SEPARATOR,
            '//' => DIRECTORY_SEPARATOR,
            '/' => DIRECTORY_SEPARATOR,
            '\\' => DIRECTORY_SEPARATOR,
        ];

        $builtFileName = str_replace(array_keys($replacements), array_values($replacements), $fileName);

        return $builtFileName;
    }

    /**
     * _moveUploadedFile
     *
     * Move Uploaded File Layer.
     *
     * @param string $source The source of the file (tmp).
     * @param string $path The path to save to.
     * @return bool
     */
    protected function _moveUploadedFile($source, $path)
    {
        return move_uploaded_file($source, $path);
    }

    /**
     * _mkdir
     *
     * Mk Dir Layer.
     *
     * @param string $pathname The path to save to.
     * @param int $mode Mode.
     * @param bool $recursive Recursive.
     * @return bool
     */
    protected function _mkdir($pathname, $mode, $recursive)
    {
        return mkdir($pathname, $mode, $recursive);
    }
}
