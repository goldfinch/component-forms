<?php

namespace Goldfinch\Component\Forms\Blocks;

use Goldfinch\Fielder\Fielder;
use Goldfinch\Blocks\Models\BlockElement;
use Goldfinch\Component\Forms\Models\FormSegment;

class FormBlock extends BlockElement
{
    private static $table_name = 'FormBlock';
    private static $singular_name = 'Form';
    private static $plural_name = 'Form';

    private static $db = [];

    private static $inline_editable = false;
    private static $description = '';
    private static $icon = 'font-icon-block-form';
    // private static $disable_pretty_anchor_name = false;
    // private static $displays_title_in_template = true;

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
