<?php

namespace Goldfinch\Component\Forms\Commands;

use Goldfinch\Taz\Console\GeneratorCommand;

#[AsCommand(name: 'vendor:component-forms:ext:admin')]
class FormsAdminExtensionCommand extends GeneratorCommand
{
    protected static $defaultName = 'vendor:component-forms:ext:admin';

    protected $description = 'Create FormsAdmin extension';

    protected $path = '[psr4]/Extensions';

    protected $type = 'extension';

    protected $stub = './stubs/formsadmin-extension.stub';

    protected $prefix = 'Extension';
}
