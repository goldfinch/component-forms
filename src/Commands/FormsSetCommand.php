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
        $command = $this->getApplication()->find('vendor:component-forms:ext:admin');
        $command->run(new ArrayInput(['name' => 'FormsAdmin']), $output);

        $command = $this->getApplication()->find('vendor:component-forms:ext:config');
        $command->run(new ArrayInput(['name' => 'FormConfig']), $output);

        $command = $this->getApplication()->find('vendor:component-forms:ext:block');
        $command->run(new ArrayInput(['name' => 'FormBlock']), $output);

        $command = $this->getApplication()->find('vendor:component-forms:ext:segment');
        $command->run(new ArrayInput(['name' => 'FormSegment']), $output);

        $command = $this->getApplication()->find('vendor:component-forms:config');
        $command->run(new ArrayInput(['name' => 'component-forms']), $output);

        $command = $this->getApplication()->find('vendor:component-forms:templates');
        $command->run(new ArrayInput([]), $output);

        return Command::SUCCESS;
    }
}
