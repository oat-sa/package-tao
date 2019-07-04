# What's new in TAO 3.3?

## General BackOffice
- Added "Move to" and "Copy to" functionality to Items and Tests
- Moved custom properties editing to Manage Schema
- Updated and added languages provided by Community Members, e.g. Dutch

## BackOffice Item Authoring
- Added table editing in item content
- Improved media (re)sizing functionality
- Added tooltip options for providing glossary (ARIA describedby)
- Added "Language" setting on item content for instruction for (external) text-to-speech tools
- The Translate feature will be hidden if data-language is locked 

## BackOffice Test Authoring
- Added "Publish" action for quick delivery creation
- New item selection tool on test-editor for easy searching and filtering of items to insert
- You can now use scoring variables for test level feedback in Rubric Blocks

## BackOffice Results Management
- Export to CSV of entire tree structure
- Optional inclusion of delivery and test-taker metadata in CSV export
- The platform now allows LTI launch of item review screen

## Test Taker Experience
- Added keyboard control on new test-runner
- Added Security plugins: light lock-down (forced full-screen)
- Color contrast tool available by default for accessibility
- You'll now receive a warning on submitting items in a linear test
- You can now preserve highlighter data as part of result data

## New API calls 
- REST API call to delete deliveries 
- REST API to list deliveries 
- REST API to manage classes 

## General Performance & Experience 
- Implemented task queue for additional longer running operations, such as import/export 
- Implemented task queue: You can now cancel tasks before they are executed
- Implemented task queue: You can now directly access resources on task completion
- Implemented security override on LTI test launch
- Implemented media sizer improvements to resize and reset images 
- Implemented test runner optimisations
- Many bugfixes and security patches


## User and Administrator Guides

The latest version can be found at:
- [User Guide](https://userguide.taotesting.com)
- [Administrator Guide](https://adminguide.taotesting.com)
