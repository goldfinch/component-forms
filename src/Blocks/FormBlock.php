<?php

namespace Goldfinch\Component\Forms\Blocks;

use DNADesign\Elemental\Models\BaseElement;
use Goldfinch\Helpers\Traits\BaseElementTrait;
use Goldfinch\Component\Forms\Models\FormSegment;

class FormBlock extends BaseElement
{
    use BaseElementTrait;

    private static $table_name = 'FormBlock';
    private static $singular_name = 'Form';
    private static $plural_name = 'Form';

    private static $inline_editable = false;
    private static $description = 'Form block handler';
    private static $icon = 'font-icon-block-form';

    private static $has_one = [
        'Segment' => FormSegment::class,
    ];

    private static $owns = ['Segment'];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields()->initFielder($this);

        $fielder = $fields->getFielder();

        $fielder->fields([
            'Root.Main' => [$fielder->objectLink('Segment')],
        ]);

        return $fields;
    }
}
