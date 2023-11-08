<?php

namespace Goldfinch\Component\Forms\Admin;

use SilverStripe\Admin\ModelAdmin;
use JonoM\SomeConfig\SomeConfigAdmin;
use Goldfinch\Component\Forms\Blocks\FormBlock;
use Goldfinch\Component\Forms\Models\FormRecord;
use Goldfinch\Component\Forms\Configs\FormConfig;
use Goldfinch\Component\Forms\Models\FormSegment;
use SilverStripe\Forms\GridField\GridFieldConfig;

class FormsAdmin extends ModelAdmin
{
    use SomeConfigAdmin;

    private static $url_segment = 'forms';
    private static $menu_title = 'Forms';
    private static $menu_icon_class = 'bi-send-fill';
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

    // public $showImportForm = true;
    // public $showSearchForm = true;
    // private static $page_length = 30;

    public function getList()
    {
        $list = parent::getList();

        // ..

        return $list;
    }

    protected function getGridFieldConfig(): GridFieldConfig
    {
        $config = parent::getGridFieldConfig();

        // ..

        return $config;
    }

    public function getSearchContext()
    {
        $context = parent::getSearchContext();

        // ..

        return $context;
    }

    public function getEditForm($id = null, $fields = null)
    {
        $form = parent::getEditForm($id, $fields);

        // ..

        return $form;
    }

    // public function getExportFields()
    // {
    //     return [
    //         // 'Name' => 'Name',
    //         // 'Category.Title' => 'Category'
    //     ];
    // }
}
