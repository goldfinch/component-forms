<?php

namespace Goldfinch\Component\Forms\Commands;

use Goldfinch\Taz\Console\GeneratorCommand;
use Symfony\Component\Console\Command\Command;

#[AsCommand(name: 'vendor:component-forms:formsblock')]
class FormBlockExtensionCommand extends GeneratorCommand
{
    protected static $defaultName = 'vendor:component-forms:formsblock';

    protected $description = 'Create FormsBlock extension';

    protected $path = '[psr4]/Extensions';

    protected $type = 'component-forms block extension';

    protected $stub = 'formsblock-extension.stub';

    protected $prefix = 'Extension';

    protected function execute($input, $output): int
    {
        parent::execute($input, $output);

        return Command::SUCCESS;
    }
}
