<?php

namespace Goldfinch\Component\Forms\Commands;

use Goldfinch\Taz\Console\GeneratorCommand;

#[AsCommand(name: 'make:form-request')]
class MakeFormRequestCommand extends GeneratorCommand
{
    protected static $defaultName = 'make:form-request';

    protected $description = 'Create new form reqeust';

    protected $path = 'app/src/Requests';

    protected $type = 'form request';

    protected $stub = './stubs/form-request.stub';

    protected $prefix = 'Request';
}
