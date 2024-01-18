<?php

namespace Goldfinch\Component\Forms\Admin;

use SilverStripe\Admin\ModelAdmin;
use JonoM\SomeConfig\SomeConfigAdmin;
use Goldfinch\Component\Forms\Blocks\FormBlock;
use Goldfinch\Component\Forms\Models\FormRecord;
use Goldfinch\Component\Forms\Configs\FormConfig;
use Goldfinch\Component\Forms\Models\FormSegment;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;

class FormsAdmin extends ModelAdmin
{
    use SomeConfigAdmin;

    private static $url_segment = 'forms';
    private static $menu_title = 'Forms';
    private static $menu_icon_class = 'font-icon-block-form';
    // private static $menu_priority = -0.5;

    private static $managed_models = [
        FormSegment::class => [
            'title'=> 'Segments',
        ],
        FormRecord::class => [
            'title'=> 'Records',
        ],
        FormBlock::class => [
            'title'=> 'Blocks',
        ],
        FormConfig::class => [
            'title'=> 'Settings',
        ],
    ];

    protected function getGridFieldConfig(): GridFieldConfig
    {
        $config = parent::getGridFieldConfig();

        if ($this->modelClass == FormRecord::class)
        {
            $config->removeComponentsByType(GridFieldAddNewButton::class);
        }

        return $config;
    }
}
