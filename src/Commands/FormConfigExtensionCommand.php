<?php

namespace Goldfinch\Component\Forms\Commands;

use Goldfinch\Taz\Console\GeneratorCommand;

#[AsCommand(name: 'vendor:component-forms:ext:config')]
class FormConfigExtensionCommand extends GeneratorCommand
{
    protected static $defaultName = 'vendor:component-forms:ext:config';

    protected $description = 'Create FormConfig extension';

    protected $path = '[psr4]/Extensions';

    protected $type = 'extension';

    protected $stub = './stubs/formconfig-extension.stub';

    protected $prefix = 'Extension';
}
