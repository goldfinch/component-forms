1)

app/_config/component-forms.yml
```
---
Name: app-component-form
---

Goldfinch\Component\Form\Models\FormSegment:
  segment_types:
    contact:
      label: 'Contact form'
      settings: true
      records: true
    newsletter:
      label: 'Newsletter'
      settings: false
      records: false
```

2)

app/_schema/form-{segment_type}.json
```
{
    "type": "array",
    "options": {},
    "items": {
        "type": "object",
        "properties": {
            "example": {
                "title": "Example",
                "type": "string",
                "default": "default example text"
              }
        }
      }

  }
```

3)

themes/{theme}/templates/Components/Form/{segment_type}.ss

```
my custom template for specific segment type
```
