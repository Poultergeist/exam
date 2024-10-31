# Extra Field Plus - Extra Field Settings Provider

The Extra Field Settings Provider module provides an interface and the extra
field base plugins with editable display settings.


## Table of contents

- Requirements
- Installation
- Configuration
- Maintainers


## Requirements

This module requires the following modules:

- [Extra Field](https://www.drupal.org/project/extra_field)


## Installation

Install the Extra Field Settings Provider module as you would normally
install a contributed Drupal module. Visit
https://www.drupal.org/node/1897420 for further information.


## Configuration

The module does not have a configuration page. You can find all extra field
plugins at Reports > Extra Field Plugins List.


### Upgrading to 3.x

If you're upgrading from 8.x-1.x or 8.x-2.x you have to make some changes to
your custom Extra Field Display plugins!
Sadly these breaking changes (BC) were required. See UPGRADE.md for details
and required code changes!


## Uses

To provide the extra field plugin with display settings you must at least
implement the ExtraFieldPlusDisplayInterface.

But there are two base plugins which can help you with this. Just extend
ExtraFieldPlusDisplayBase or ExtraFieldPlusDisplayFormattedBase plugins.

All yours extra field plugins have to be placed in
your_custom_module/src/Plugin/ExtraField/Display folder.

Example plugins with simple and formatted output are in extra_field_plus_example
module.


## Maintainers

- Andrew Tsiupiakh - [andrew_tspkh](https://www.drupal.org/u/andrew_tspkh)
- Julian Pustkuchen - [Anybody](https://www.drupal.org/u/anybody)
- Joshua Sedler - [Grevil](https://www.drupal.org/u/grevil)

Supporting organizations:

- Drupal Ukraine Community - https://www.drupal.org/drupal-ukraine-community
- Smile - https://www.drupal.org/smile
