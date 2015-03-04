<?php

namespace Utils\Shell;

use Cake\Console\Shell;


/**
 * Plugin shell command.
 */
class PluginShell extends Shell
{

    /**
     * Tasks to load
     *
     * @var type
     */
    public $tasks = [
        'Utils.Load',
        'Utils.Unload'
    ];

    /**
     * main() method.
     *
     * @return bool|int Success or error code.
     */
    public function main()
    {
    }

    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->addSubcommand('load', [
            'help'   => 'Loads a plugin',
            'parser' => $this->Load->getOptionParser(),
        ]);
        $parser->addSubcommand('unload', [
            'help'   => 'Unloads a plugin',
            'parser' => $this->Unload->getOptionParser(),
        ]);
        return $parser;
    }

}
