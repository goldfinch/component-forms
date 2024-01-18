<?php

namespace Goldfinch\Component\Forms\Commands;

use Goldfinch\Taz\Console\GeneratorCommand;
use Symfony\Component\Console\Command\Command;

#[AsCommand(name: 'config:component-forms')]
class ComponentFormsConfigCommand extends GeneratorCommand
{
    protected static $defaultName = 'config:component-forms';

    protected $description = 'Create component-forms config';

    protected $path = 'app/_config';

    protected $type = 'component-forms yml config';

    protected $stub = 'formconfig.stub';

    protected $extension = '.yml';

    protected function execute($input, $output): int
    {
        parent::execute($input, $output);

        return Command::SUCCESS;
    }
}
