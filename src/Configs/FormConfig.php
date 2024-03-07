<?php

namespace Goldfinch\Component\Forms\Configs;

use JonoM\SomeConfig\SomeConfig;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\TemplateGlobalProvider;

class FormConfig extends DataObject implements TemplateGlobalProvider
{
    use SomeConfig;

    private static $table_name = 'FormConfig';

    private static $db = [
        'DisabledRecords' => 'Boolean',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fielder = $fields->fielder($this);

        $fielder->fields([
            'Root.Main' => [
                $fielder->checkbox('DisabledRecords', 'Disable log records'),
            ],
        ]);

        return $fields;
    }
}
