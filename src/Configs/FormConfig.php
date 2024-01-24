<?php

namespace Goldfinch\Component\Forms\Configs;

use Goldfinch\Fielder\Fielder;
use JonoM\SomeConfig\SomeConfig;
use SilverStripe\ORM\DataObject;
use Goldfinch\Fielder\Traits\FielderTrait;
use SilverStripe\View\TemplateGlobalProvider;

class FormConfig extends DataObject implements TemplateGlobalProvider
{
    use SomeConfig, FielderTrait;

    private static $table_name = 'FormConfig';

    private static $db = [
        'DisabledRecords' => 'Boolean',
    ];

    public function fielder(Fielder $fielder): void
    {
        $fielder->fields([
            'Root.Main' => [
                $fielder->checkbox('DisabledRecords', 'Disable log records'),
            ],
        ]);
    }
}
