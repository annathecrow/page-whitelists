=== Page Whitelists ===
Contributors: corvidism
Tags: pages, user access, user management, editing pages, deleting pages, access management, admin tools, 
Requires at least: 3.6
Tested up to: 4.0.0
Stable tag: 1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin enables admins to limit user access only to selected pages by creating whitelists and assign them to users or roles.

== Description ==

Page Whitelists is an administration tool that can be used to allow selected users to edit only certain pages, leaving the rest inaccessible. This is done by creating "whitelists", and assigning them to users and/or roles. Each whitelist can also limit creation of new pages.Every page, user and role can belong to multiple whitelists. 

Combining whitelists:
*  Whitelists are additive - every user has access to all pages in all whitelists they're assigned to.
*  'Strict' whitelists have priority - once a user is assigned to a whitelist that disables creation of new pages, they are not allowed to do so (even if other whitelists are 'non-strict').

**NOTICE**: This plugin is new, and as such its "field testing" was minimal so far. Use with caution, don't base multimillion investments on it, you know the drill.

== Installation ==

1. Install and activate like any other plugin. 
1. Create a whitelist in Settings->Page Whitelists.
1. Add users/roles and pages to it. 

== Changelog ==

= 1.2 =
Bug fix - automatic addition of newly created pages to non-strict whitelists now works.

== Upgrade Notice ==

= 1.2 =
Version fixes a bug in the non-strict whitelist functionality. Update recomended.

= 1.0 =
First published version.