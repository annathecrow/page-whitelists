page-whitelists
===============


Requires at least: 3.6
Tested up to: 3.9.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


Wordpress plugin limiting user access to pages. Allows administrators to create whitelists and assign them to either single users, or roles, and set if users are allowed to create new pages or not.

## Description

Page Whitelists is an administration tool that can be used to allow selected users to edit only certain pages, leaving the rest inaccessible. This is done by creating "whitelists", and assigning them to users and/or roles. Each whitelist can also limit creation of new pages.Every page, user and role can belong to multiple whitelists. 

#### Combining whitelists:
*  Whitelists are additive - every user has access to all pages in all whitelists they're assigned to.
*  'Strict' whitelists have priority - once a user is assigned to a whitelist that disables creation of new pages, they are not allowed to do so (even if other whitelists are 'non-strict').

**NOTICE**: This plugin is new, and as such its "field testing" was minimal so far. Use with caution, don't base multimillion investments on it, you know the drill.

## Installation

1. Install and activate like any other plugin. 
1. Create a whitelist in Settings->Page Whitelists.
1. Add users/roles and pages to it. 

## Upgrade Notice

### 1.0
First published version.