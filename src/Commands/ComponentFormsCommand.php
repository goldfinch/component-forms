<?php

namespace Goldfinch\Component\Forms\Commands;

use Goldfinch\Taz\Console\GeneratorCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;

#[AsCommand(name: 'vendor:component-forms')]
class ComponentFormsCommand extends GeneratorCommand
{
    protected static $defaultName = 'vendor:component-forms';

    protected $description = 'Populate goldfinch/component-forms components';

    protected function execute($input, $output): int
    {
        $command = $this->getApplication()->find(
            'vendor:component-forms:formsegment',
        );
        $input = new ArrayInput(['name' => 'FormSegment']);
        $command->run($input, $output);

        $command = $this->getApplication()->find(
            'vendor:component-forms:formconfig',
        );
        $input = new ArrayInput(['name' => 'FormConfig']);
        $command->run($input, $output);

        $command = $this->getApplication()->find(
            'vendor:component-forms:formsblock',
        );
        $input = new ArrayInput(['name' => 'FormsBlock']);
        $command->run($input, $output);

        $command = $this->getApplication()->find('vendor:component-forms:templates');
        $input = new ArrayInput([]);
        $command->run($input, $output);

        $command = $this->getApplication()->find('vendor:component-forms:config');
        $input = new ArrayInput(['name' => 'component-forms']);
        $command->run($input, $output);

        return Command::SUCCESS;
    }
}
