extension-tao-testqti
=====================

Extension to create QTI tests into TAO


About the new test runner
=========================

The new test runner uses now a more consistent format for the config, but a mapping is made to convert the current server config to the new format. So if new entries are added to current config, the class has to be updated to support this new entry.

Now the review plugin is related to the item categories, so the category `x-tao-option-reviewScreen` need to be set on each navigable item. The mark for review button is related to the category `x-tao-option-markReview`

Here is a list of known category options:

| Option | Description |
| --- | --- |
| `x-tao-option-reviewScreen` | Enable the review/navigation panel |
| `x-tao-option-markReview` | Enable the mark for review button when the review/navigation panel is enabled |
| `x-tao-option-exit` | Allow to finish and exit the test |
| `x-tao-option-nextSection` | Enable the next section button |
| `x-tao-option-nextSectionWarning` | Enable the next section button, display a confirm message |