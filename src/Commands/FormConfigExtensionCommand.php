<?php

namespace Goldfinch\Component\Forms\Commands;

use Goldfinch\Taz\Console\GeneratorCommand;
use Symfony\Component\Console\Command\Command;

#[AsCommand(name: 'vendor:component-forms:formconfig')]
class FormConfigExtensionCommand extends GeneratorCommand
{
    protected static $defaultName = 'vendor:component-forms:formconfig';

    protected $description = 'Create FormConfig extension';

    protected $path = '[psr4]/Extensions';

    protected $type = 'component-forms config extension';

    protected $stub = 'formconfig-extension.stub';

    protected $prefix = 'Extension';

    protected function execute($input, $output): int
    {
        parent::execute($input, $output);

        return Command::SUCCESS;
    }
}
