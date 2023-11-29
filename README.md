1)

app/_config/component-forms.yml
```---
Name: app-component-forms
---

Goldfinch\Component\Forms\Models\FormSegment:
  segment_types:
    contact:
      label: 'Contact form'
      settings: true
      records: true
      records_fields:
        - name
        - email
        - phone
        - message
        - newsletter
        - how
      supplies_fields:
        - how_options
      replacable_data:
        - name
        - email
    newsletter:
      label: 'Newsletter'
      settings: false
      records: false
```

2)

app/_schema/form-{segment_type}.json
```
{
    "type": "object",
    "properties": {
      "how_options": {
            "title": "How did you hear about us? (Options)",
            "type": "array",
            "options": {},
            "items": {
                "type": "object",
                "properties": {
                    "label": {
                        "title": "Title",
                        "type": "string"
                    }
                }
            }

        }
    }
}
```

3)

themes/{theme}/templates/Components/Forms/{segment_type}.ss

```
my custom template for specific segment type

<div class="container">
  <div app-contact-form data-supplies="$FormSupplies"></div>
</div>
```
