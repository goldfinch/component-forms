<?php

namespace Goldfinch\Component\Forms\Commands;

use Goldfinch\Taz\Console\GeneratorCommand;

#[AsCommand(name: 'vendor:component-forms:config')]
class FormsConfigCommand extends GeneratorCommand
{
    protected static $defaultName = 'vendor:component-forms:config';

    protected $description = 'Create Forms YML config';

    protected $path = 'app/_config';

    protected $type = 'config';

    protected $stub = './stubs/config.stub';

    protected $extension = '.yml';
}
