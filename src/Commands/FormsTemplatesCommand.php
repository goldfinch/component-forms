<?php

namespace Goldfinch\Component\Forms\Commands;

use Goldfinch\Taz\Services\Templater;
use Goldfinch\Taz\Console\GeneratorCommand;

#[AsCommand(name: 'vendor:component-forms:templates')]
class FormsTemplatesCommand extends GeneratorCommand
{
    protected static $defaultName = 'vendor:component-forms:templates';

    protected $description = 'Publish [goldfinch/component-forms] templates';

    protected $no_arguments = true;

    protected function execute($input, $output): int
    {
        $templater = Templater::create($input, $output, $this, 'goldfinch/component-forms');

        $theme = $templater->defineTheme();

        if (is_string($theme)) {

            $componentPath = BASE_PATH . '/vendor/goldfinch/component-forms/templates/Goldfinch/Component/Forms/';
            $themePath = 'themes/' . $theme . '/templates/Goldfinch/Component/Forms/';

            $files = [
                [
                    'from' => $componentPath . 'Blocks/FormBlock.ss',
                    'to' => $themePath . 'Blocks/FormBlock.ss',
                ],
                [
                    'from' => $componentPath . 'FormSegment.ss',
                    'to' => $themePath . 'FormSegment.ss',
                ],
                [
                    'from' => $componentPath . 'ThankYou.ss',
                    'to' => $themePath . 'ThankYou.ss',
                ],
            ];

            return $templater->copyFiles($files);
        } else {
            return $theme;
        }
    }
}
