<?php

namespace Goldfinch\Component\Forms\Commands;

use Symfony\Component\Finder\Finder;
use Goldfinch\Taz\Services\Templater;
use Goldfinch\Taz\Services\InputOutput;
use Goldfinch\Taz\Console\GeneratorCommand;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;

#[AsCommand(name: 'make:form-segment')]
class MakeFormSegmentCommand extends GeneratorCommand
{
    protected static $defaultName = 'make:form-segment';

    protected $description = 'Make new form segment';

    protected function execute($input, $output): int
    {
        $io = new InputOutput($input, $output);

        $segmentName = $io->question('Name of the segment (lowercase, dash, A-z0-9)', null, function ($answer) use ($io) {

            if (!is_string($answer) || $answer === null) {
                throw new \RuntimeException(
                    'Invalid name'
                );
            } else if (strlen($answer) < 2) {
                throw new \RuntimeException(
                    'Too short name'
                );
            } else if(!preg_match('/^([A-z0-9\-]+)$/', $answer)) {
                throw new \RuntimeException(
                    'Name can contains letter, numbers and dash'
                );
            }

            return $answer;
        });

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

        if (!$this->setSegmentInConfig($segmentName)) {
            // create config

            $command = $this->getApplication()->find('vendor:component-forms:config');

            $arguments = [
                'name' => 'component-forms',
            ];

            $greetInput = new ArrayInput($arguments);
            $returnCode = $command->run($greetInput, $output);

            $this->setSegmentInConfig($segmentName);
        }

        $io->right('Form segment has been added');

        return Command::SUCCESS;
    }

    private function setSegmentInConfig($segmentName)
    {
        $rewritten = false;

        $finder = new Finder();
        $files = $finder->in(BASE_PATH . '/app/_config')->files()->contains('Goldfinch\Component\Forms\Models\FormSegment');

        foreach ($files as $file) {

            // stop after first replacement
            if ($rewritten) {
                break;
            }

            if (strpos($file->getContents(), 'segment_types') !== false) {

                $ucfirst = ucfirst($segmentName);

                $newContent = $this->addToLine(
                    $file->getPathname(),
                    'segment_types:','    '.$segmentName.':'.PHP_EOL.'      label: "'.$ucfirst.' form"'.PHP_EOL.'      settings: true'.PHP_EOL.'      records: true'.PHP_EOL.'      records_fields:'.PHP_EOL.'        - name'.PHP_EOL.'        - email'.PHP_EOL.'        - phone'.PHP_EOL.'        - message'.PHP_EOL.'        - newsletter'.PHP_EOL.'        - how'.PHP_EOL.'      supplies_fields:'.PHP_EOL.'        - how_options'.PHP_EOL.'      replacable_data:'.PHP_EOL.'        - name'.PHP_EOL.'        - email'.PHP_EOL.'      vue:'.PHP_EOL.'        component: "'.$ucfirst.'Form"'.PHP_EOL.'        action: "'.$segmentName.'"'.PHP_EOL.'        url: "/api/req/'.$segmentName.'"'.PHP_EOL.'        id: "form-'.$segmentName.'"'.PHP_EOL.'        testmode: true',
                );

                file_put_contents($file->getPathname(), $newContent);

                $rewritten = true;
            }
        }

        return $rewritten;
    }
}
