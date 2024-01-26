<?php

namespace Goldfinch\Component\Forms\Commands;

use Goldfinch\Taz\Console\GeneratorCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;

#[AsCommand(name: 'vendor:component-forms')]
class FormsSetCommand extends GeneratorCommand
{
    protected static $defaultName = 'vendor:component-forms';

    protected $description = 'Set of all [goldfinch/component-forms] commands';

    protected $no_arguments = true;

    protected function execute($input, $output): int
    {
        $command = $this->getApplication()->find(
            'vendor:component-forms:ext:admin',
        );
        $input = new ArrayInput(['name' => 'FormsAdmin']);
        $command->run($input, $output);

        $command = $this->getApplication()->find(
            'vendor:component-forms:ext:config',
        );
        $input = new ArrayInput(['name' => 'FormConfig']);
        $command->run($input, $output);

        $command = $this->getApplication()->find(
            'vendor:component-forms:ext:block',
        );
        $input = new ArrayInput(['name' => 'FormBlock']);
        $command->run($input, $output);

        $command = $this->getApplication()->find(
            'vendor:component-forms:ext:segment',
        );
        $input = new ArrayInput(['name' => 'FormSegment']);
        $command->run($input, $output);

        $command = $this->getApplication()->find('vendor:component-forms:config');
        $input = new ArrayInput(['name' => 'component-forms']);
        $command->run($input, $output);

        $command = $this->getApplication()->find('vendor:component-forms:templates');
        $input = new ArrayInput([]);
        $command->run($input, $output);

        return Command::SUCCESS;
    }
}
