<?php

namespace Goldfinch\Component\Forms\Commands;

use Goldfinch\Taz\Console\GeneratorCommand;
use Symfony\Component\Console\Command\Command;

#[AsCommand(name: 'vendor:component-forms:formsegment')]
class FormSegmentExtensionCommand extends GeneratorCommand
{
    protected static $defaultName = 'vendor:component-forms:formsegment';

    protected $description = 'Create FormSegment extension';

    protected $path = '[psr4]/Extensions';

    protected $type = 'component-forms item extension';

    protected $stub = 'formsegment-extension.stub';

    protected $prefix = 'Extension';

    protected function execute($input, $output): int
    {
        parent::execute($input, $output);

        return Command::SUCCESS;
    }
}
