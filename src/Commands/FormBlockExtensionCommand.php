<?php

namespace Goldfinch\Component\Forms\Commands;

use Goldfinch\Taz\Console\GeneratorCommand;
use Symfony\Component\Console\Command\Command;

#[AsCommand(name: 'vendor:component-forms:ext:block')]
class FormBlockExtensionCommand extends GeneratorCommand
{
    protected static $defaultName = 'vendor:component-forms:ext:block';

    protected $description = 'Create FormBlock extension';

    protected $path = '[psr4]/Extensions';

    protected $type = 'extension';

    protected $stub = './stubs/formblock-extension.stub';

    protected $prefix = 'Extension';

    protected function execute($input, $output): int
    {
        parent::execute($input, $output);

        return Command::SUCCESS;
    }
}
