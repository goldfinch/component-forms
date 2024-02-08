<?php

namespace Goldfinch\Component\Forms\Commands;

use Goldfinch\Taz\Services\Templater;
use Goldfinch\Taz\Console\GeneratorCommand;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;

#[AsCommand(name: 'make:form-segment')]
class MakeFormSegmentCommand extends GeneratorCommand
{
    protected static $defaultName = 'make:form-segment';

    protected $description = 'Make new form segment';

    protected $no_arguments = true;

    protected function execute($input, $output): int
    {
        $segmentName = $this->askClassNameQuestion('Name of the segment (eg: Contact, Newsletter)', $input, $output);

        if (!$segmentName) {
            return Command::FAILURE;
        }

        $segmentName = strtolower($segmentName);

        $fs = new Filesystem();

        $templater = Templater::create($input, $output, $this, 'goldfinch/component-forms');
        $theme = $templater->defineTheme();

        $fs->copy(
            BASE_PATH .
                '/vendor/goldfinch/component-forms/components/segment.json',
            'app/_schema/form-'.$segmentName.'.json',
        );

        $fs->copy(
            BASE_PATH .
                '/vendor/goldfinch/component-forms/components/segment.ss',
            'themes/'.$theme.'/templates/Components/Forms/'.$segmentName.'.ss',
        );

        // find config
        $config = $this->findYamlConfigFileByName('app-component-forms');

        // create new config if not exists
        if (!$config) {

            $command = $this->getApplication()->find('make:config');
            $command->run(new ArrayInput([
                'name' => 'component-forms',
                '--plain' => true,
                '--after' => 'goldfinch/component-forms',
                '--nameprefix' => 'app-',
            ]), $output);

            $config = $this->findYamlConfigFileByName('app-component-forms');
        }

        $ucfirst = ucfirst($segmentName);

        // update config
        $this->updateYamlConfig(
            $config,
            'Goldfinch\Component\Forms\Models\FormSegment' . '.segment_types.' . $segmentName,
            [
                'label' => $ucfirst . ' form',
                'settings' => true,
                'records' => true,
                'records_fields' => [
                    'name',
                    'email',
                    'phone',
                    'message',
                    'newsletter',
                    'how',
                ],
                'supplies_fields' => [
                    'how_options',
                ],
                'replacable_data' => [
                    'name',
                    'email',
                ],
                'vue' => [
                    'component' => $ucfirst . 'Form',
                    'action' => $segmentName,
                    'url' => 'api/req/' . $segmentName,
                    'id' => 'form-' . $segmentName,
                    'testmode' => true,
                ]
            ],
        );

        return Command::SUCCESS;
    }
}
