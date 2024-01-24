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

    public function getSummary()
    {
        return $this->getDescription();
    }

    public function getType()
    {
        $default = $this->i18n_singular_name() ?: 'Block';

        return _t(__CLASS__ . '.BlockType', $default);
    }
}
