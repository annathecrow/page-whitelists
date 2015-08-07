# page-whitelists

Requires at least: 3.6  
Tested up to: 4.2.2  
Stable version: 3.0.1
License: GPLv2 or later  
License URI: http://www.gnu.org/licenses/gpl-2.0.html  


Wordpress plugin limiting user access to pages. Allows administrators to create whitelists and assigning them to either single users, or roles, and set if users are allowed to create new pages or not.

## Description

Page Whitelists is an administration tool that can be used to allow selected users to edit only certain pages, leaving the rest inaccessible. This is done by creating "whitelists", and assigning them to users and/or roles. Each whitelist can also limit creation of new pages.Every page, user and role can belong to multiple whitelists. 

#### Combining whitelists:
* Whitelists are additive - every user has access to all pages in all whitelists they're assigned to.
* 'Strict' whitelists have priority - once a user is assigned to a whitelist that disables creation of new pages, they are not allowed to do so (even if other whitelists are 'non-strict').

## Installation

1. Install and activate like any other plugin. 
1. Create a whitelist in Settings->Page Whitelists.
1. Add users/roles and pages to it. 

## Changelog

### 3.0.2
Bug fix (bug is only in the version from Wordpress Plugin Repository) - missing file `wp-content/plugins/page-whitelists/templates/profile_field.php` causes Fatal Error on Edit User page.

### 3.0.1
Bug fix - setting strict whitelist also blocked creation of new posts (creator-introduced bug, I am very sorry)

### 3.0
Bug fix - fixed an issue with plugins that allow creation of pages
Plugin compatibility fix - Tree Page View.
New - column with assigned whitelists in User Table
New - field on User Profile editor
New - select all/none pages when creating/editing whitelist. 

### 2.0
Bug fix - plugin now doesn't fail on screen-less admin pages (various AJAX helpers etc.)
New - plugin now filters all backend queries that request pages (including those made by AJAX).

#### 1.2
Bug fix - automatic addition of newly created pages to non-strict whitelists now works.

#### 1.2 
Version fixes a bug in the non-strict whitelist functionality. Update recomended.

#### 1.0
First published version.

## FAQ

### What happens when user is assigned more than one whitelist? 
Whitelists are additive - every user has access to all pages in all whitelists they're assigned to. 'Strict' whitelists have priority - once a user is assigned to a whitelist that disables creation of new pages, they are not allowed to do so (even if other whitelists are 'non-strict').

