<?php

namespace Goldfinch\Component\Forms\Commands;

use Goldfinch\Taz\Console\GeneratorCommand;

#[AsCommand(name: 'vendor:component-forms:ext:segment')]
class FormSegmentExtensionCommand extends GeneratorCommand
{
    protected static $defaultName = 'vendor:component-forms:ext:segment';

    protected $description = 'Create FormSegment extension';

    protected $path = '[psr4]/Extensions';

    protected $type = 'extension';

    protected $stub = './stubs/formsegment-extension.stub';

    protected $suffix = 'Extension';
}
