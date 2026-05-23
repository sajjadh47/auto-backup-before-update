=== Auto Backup Before Update ===
Tags: backup, rollback, plugin update, auto backup, restore
Contributors: sajjad67
Author: Sajjad Hossain Sagor
Requires at least: 6.5
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.0.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Automatically backs up each plugin and theme before updates — allowing quick rollback to previous versions if anything goes wrong.

== Description ==
**Auto Backup Before Update** automatically creates version-specific backups of your plugins and themes **right before they’re updated** — so you can safely revert if an update breaks your site.

No more guessing what went wrong after an update — simply restore to the last working version in a few clicks.

### Key Features
- **Automatic pre-update backups** — Every plugin and theme is backed up before updates.
- **Version-specific restore points** — Restore individual plugins or themes to their previous versions.
- **Manual restore option** — Revert anytime from the Restore screen if an issue occurs.
- **Lightweight and reliable** — No full-site backups or heavy compression that slow your server.

### How It Works
1. When you update a plugin or theme, this plugin creates a backup snapshot of:
   - The specific plugin or theme files being updated.
2. After the update, if the site breaks or an issue occurs, go to:
   **Dashboard → ABBU → Restore**
3. Choose the item and version to restore — and you’re back online instantly.

### Ideal For
- Site owners who frequently update plugins/themes.
- Developers managing multiple client sites.
- Staging or production sites where downtime isn’t acceptable.

== Installation ==
1. Upload the plugin folder to `/wp-content/plugins/auto-backup-before-update/` or install directly from the WordPress Plugin Directory.
2. Activate the plugin through the ‘Plugins’ menu in WordPress.
3. Backups are automatically triggered before plugin, theme, or core updates.
4. To restore, navigate to **Dashboard → ABBU → Restore**.

== Screenshots ==
1. Backup settings and restore panel.
2. Version-specific plugin restore options.
3. Version-specific theme restore options.

== Frequently Asked Questions ==
= Does this create full site backups? =
No. It only backs up the specific plugin or theme being updated — keeping things lightweight and fast.

= Where are backups stored? =
Backups are saved locally in `wp-content/uploads/abbu-backups/`. You can download or delete them anytime.

= Can I restore a specific plugin version only? =
Yes. Each plugin and theme backup is tied to its previous version, allowing selective rollback.

= Does it work with auto-updates? =
Yes, backups are created automatically before both manual and scheduled auto-updates.

== Changelog ==
= 1.0.2 =
- Compatibility checkup for latest wp version 7.0
= 1.0.1 =
- Compatibility checkup for latest wp version 6.9
= 1.0.0 =
* Initial release
* Automatic pre-update backups for plugins and themes
* One-click restore with version tracking

== Upgrade Notice ==
Always try to keep your plugin update so that you can get the improved and additional features added to this plugin up to date.