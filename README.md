# Auto Backup Before Update

[![Plugin Banner](https://ps.w.org/auto-backup-before-update/assets/banner-1544x500.png)](https://wordpress.org/plugins/auto-backup-before-update/)

**Tags:** backup, rollback, plugin update, auto backup, restore \
**Tested up to:** 7.1 \
**Requires PHP:** 7.4

Automatically backs up each plugin and theme before updates — allowing quick rollback to previous versions if anything goes wrong.

## Description

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
   **Dashboard → Tools → Auto Backup Before Update → Restore**
3. Choose the item and version to restore — and you’re back online instantly.

### Ideal For
- Site owners who frequently update plugins/themes.
- Developers managing multiple client sites.
- Staging or production sites where downtime isn’t acceptable.

## Installation

To add a WordPress Plugin using the built-in plugin installer:

Go to Plugins > Add New.

1. Type in the name "Auto Backup Before Update" in Search Plugins box
2. Find the "Auto Backup Before Update" Plugin to install.
3. Click Install Now to begin the plugin installation.
4. The resulting installation screen will list the installation as successful or note any problems during the install.
If successful, click Activate Plugin to activate it, or Return to Plugin Installer for further actions.

## Frequently Asked Questions

### Does this create full site backups?
No. It only backs up the specific plugin or theme being updated — keeping things lightweight and fast.

### Where are backups stored?
Backups are saved locally in `wp-content/uploads/abbu-backups/`. You can download or delete them anytime.

### Can I restore a specific plugin version only?
Yes. Each plugin and theme backup is tied to its previous version, allowing selective rollback.

### Does it work with auto-updates?
Yes, backups are created automatically before both manual and scheduled auto-updates.

## Screenshots

### 1. screenshot-1

![screenshot-1](https://ps.w.org/auto-backup-before-update/assets/screenshot-1.png)

### 2. screenshot-2

![screenshot-2](https://ps.w.org/auto-backup-before-update/assets/screenshot-2.png)

### 3. screenshot-3

![screenshot-3](https://ps.w.org/auto-backup-before-update/assets/screenshot-3.png)

## Changelog

### 1.0.3
- Compatibility checkup for latest wp version 7.1

### 1.0.2
- Compatibility checkup for latest wp version 7.0

### 1.0.1
- Compatibility checkup for latest wp version 6.9

### 1.0.0
- Initial release.

## Upgrade Notice

Always try to keep your plugin update so that you can get the improved and additional features added to this plugin up to date.