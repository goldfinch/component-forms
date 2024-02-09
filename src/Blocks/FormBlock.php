<?php

namespace Goldfinch\Component\Forms\Blocks;

use Goldfinch\Fielder\Fielder;
use DNADesign\Elemental\Models\BaseElement;
use Goldfinch\Component\Forms\Models\FormSegment;

class FormBlock extends BaseElement
{
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

    public function fielder(Fielder $fielder): void
    {
        $fielder->fields([
            'Root.Main' => [$fielder->objectLink('Segment')],
        ]);
    }
}
