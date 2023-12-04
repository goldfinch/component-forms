<?php

namespace Goldfinch\Component\Forms\Providers;

use SilverStripe\View\TemplateGlobalProvider;
use Goldfinch\Component\Forms\Models\FormSegment;

class FormsTemplateProvider implements TemplateGlobalProvider
{
    public static function get_template_global_variables(): array
    {
        return [
            'FormSegment',
        ];
    }

    // Eg: <% with FormSegment(newsletter) %> | <% with FormSegment(3) %>
    public static function FormSegment($typeOrID)
    {
        if (is_numeric($typeOrID))
        {
            return FormSegment::get_by_id($typeOrID);
        }

        return FormSegment::get()->filter(['Type' => $typeOrID])->first();
    }
}
