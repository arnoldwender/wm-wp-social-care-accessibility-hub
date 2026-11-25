=== WM Social Care Accessibility & Kinästhetik Hub ===
Contributors: arnoldwender
Tags: accessibility, leichte sprache, pflege, sgb xi, shortcode
Requires at least: 6.4
Tested up to: 7.1
Requires PHP: 8.1
Stable tag: 1.3.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Shortcodes for care websites: SGB XI benefit amounts, an Easy Language card, an accessibility toolbar and eight example guides.

== Description ==

Building blocks for German care and support providers. The plugin does not check a site for accessibility and does not assess whether a site meets BFSG, BITV 2.0 or WCAG.

* **Care-benefit check:** `[wm_social_care_bedarfscheck]` shows the statutory maximum amounts for care degrees 1–5: Entlastungsbetrag (§ 45b SGB XI, 131 € a month), Pflegegeld (§ 37) and the joint annual budget for Verhinderungs- and Kurzzeitpflege (§ 42a, 3,539 €), as of 2025-07-01. Several checks can sit on one page.
* **Easy Language card:** `[wm_leichte_sprache_card]` prints the enclosed text in paragraphs with a Mediopunkt in 15 compound care words; it does not rewrite or check the text.
* **Accessibility toolbar:** `[wm_a11y_toolbar]` offers text size, high contrast and read aloud (browser speech synthesis, in the language of the page) for the current page view.
* **Video library:** `[wm_videothek_loesungswege]` and `[wm_video_player id="1"]` show eight built-in example guides with steps and an Easy Language summary; none of them carries a video.
* **Content model:** post type `wm_loesungsweg` with category, target-group, aid and care-degree taxonomies, exposed in the REST API. No shortcode displays these posts.
* **Ecosystem & hooks:** WordPress 6.5+ Block Bindings source `wm-social-care/info`, GenerateBlocks 2.x dynamic tag `{{wm_social_care_info}}`, action `wm_social_care_ready`, and filters `wm_social_care_bedarfscheck_result`, `wm_social_care_loesungswege_items`, and `wm_social_care_rate_limit_per_minute`.
* **REST and WP-CLI:** `GET wm-social-care/v1/loesungswege` and `/bedarfscheck` with transient-based HMAC-SHA256 IP rate limiting; `wp wm-social-care calc` and `seed` (lists the examples, writes nothing).
* **Data preservation on uninstall:** preserves all options and site content by default when deleted unless the administrator opts in via `wm_social_care_delete_data_on_uninstall` on the admin page.

Scripts and styles ship inside the plugin. Source strings are German and ship with 28 compiled gettext catalogs. Registers as a spoke of WM Suite Hub when the hub is installed and runs without it.

**No Support & Zero Liability ("AS IS"):** This plugin is provided free of charge as personal open-source software with **zero support** and **no private support**. Do not contact the author via email, `arnoldwender.com`, or direct messages. Report reproducible defects exclusively at `https://github.com/arnoldwender/wm-wp-social-care-accessibility-hub/issues` (no SLA or response guarantee). Under GPLv2+ Sections 11-12 it is distributed **"AS IS"** without warranty, legal advice, or author liability.

== Installation ==

1. Upload the `wm-social-care-accessibility-hub` folder to `/wp-content/plugins/` and activate it.
2. Place the shortcodes on the pages of your choice, or bind blocks to `wm-social-care/info`.

== Frequently Asked Questions ==

= Is technical or private support provided? =

No. This plugin comes with zero support (`0` support). Private inquiries sent via email, `arnoldwender.com`, or social/chat channels are not answered. Reproducible bugs belong solely on GitHub Issues (`https://github.com/arnoldwender/wm-wp-social-care-accessibility-hub/issues`) with no guarantee of reply or resolution. The software is provided **"AS IS"** at your sole risk with zero warranty or liability (see `SUPPORT.md` in the repository).

== Screenshots ==

1. Admin screen: which shortcodes and the post type are registered, how many Lösungswege are published, the shortcode reference and the opt-in uninstall setting
2. Frontend rendering of [wm_a11y_toolbar], [wm_leichte_sprache_card] and [wm_social_care_bedarfscheck]

== Changelog ==


= 1.3.2 =
* Fixed: the 1.3.1 translation catalogs named a mailbox as the address for reports about the source strings; they name the plugin's support forum again, as every other WM Suite plugin does.

= 1.3.1 =
* The bundled family stylesheet is identical to WM Suite Hub 1.2.5 again: it starts the admin header below WordPress's help toggle instead of shortening it. This plugin's own screens register no help tab, so they change only where another plugin adds one.
* Fixed: the admin screen said the example videos point at an external server and called the single player an HTML5 player; none of the eight built-in examples carries a video, and both texts now say what the screen and the player show.
* Fixed: in eleven languages with more than two plural forms, the count of published Lösungswege used one form for every count above one; each form now matches its count.

= 1.3.0 =
* The WM Suite Hub contract now lives in the `ArnoldWender\SuiteHub\Contracts` namespace. The spoke adapter registers only with WM Suite Hub 1.2.0 or later, so update the hub together with this plugin; used on its own, the plugin works as before.
* Renamed the plugin's own namespace to `ArnoldWender\SocialCareA11y\…` (same vendor as the hub).
* The descriptions no longer say that five examples link an external video: none carries one since 1.2.3.
* The `.wordpress-org/` banners show the current version.
* Removed: The archived pre-2026-11-19 marketing visuals in `versionen/`: they claimed features and conformity the plugin does not have. They were never part of the installable package.

= 1.2.3 =
* Removed the agency name, links to unrelated projects and the personal mailbox from code, catalogs and documentation; the admin family badge and the hub menu read "WM Suite". The READMEs say the plugin was built with an agentic workflow (AI coding agents) and should be reviewed like any third-party code.
* Added root `SUPPORT.md`, GitHub Issue templates, explicit zero-support & "AS IS" liability notices in READMEs and `readme.txt`, and normalized canonical GPLv2+ `arnoldwender.com` / `arnoldwender/wm-wp-social-care-accessibility-hub` links.
* Updated `.wordpress-org/` visual banners to personal `ARNOLD WENDER · OPEN SOURCE` branding.

= 1.2.2 =
* Add `.wordpress-org/` vector icons, banners (`772x250`, `1544x500`), live WordPress screenshots (`screenshot-1..2.png`) and `== Screenshots ==` descriptions.
* Update trilingual READMEs and `llms.txt` with live previews and direct plugin catalog link (`https://arnoldwender.com/wordpress-plugins/wm-social-care-accessibility-hub/`).

= 1.2.1 =
* Point Plugin URI to the official plugin page.

= 1.2.0 =
* Added transient-based HMAC-SHA256 IP rate limiting and strict GET/parameter verification to `wm-social-care/v1` REST routes.
* Added opt-in uninstallation setting (`wm_social_care_delete_data_on_uninstall`, default off) preserving options and content by default.
* Added WordPress 6.5+ Block Bindings source `wm-social-care/info` and GenerateBlocks 2.x dynamic tag `{{wm_social_care_info}}`.
* Added developer action `wm_social_care_ready` and filters `wm_social_care_bedarfscheck_result`, `wm_social_care_loesungswege_items`, and `wm_social_care_rate_limit_per_minute`.
* Compiled 28 `.po`/`.mo` gettext catalogs for `wm-social-care-accessibility-hub` and removed unused legacy `wm-social-care-a11y-*` catalogs.
* Added CycloneDX 1.6 `bom.json`, `.license-allowlist.yml`, WordPress Playground `blueprint.json`, and `AGENTS.md` symlink.
* Shortcode callbacks accept empty string attributes on WordPress 6.4; Easy Language card converts paragraph markup cleanly; multiple Bedarfscheck instances supported per page; catalog freshness gate added.

= 1.1.0 =
* Language catalogs, developer documentation, REST controller, spoke adapter, uninstaller.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.3.2 =
The family sheet of WM Suite Hub 1.2.5, a corrected admin caption about the example videos and plural forms in eleven translations, first released in 1.3.1, with corrected catalog headers. No data or settings change.

= 1.3.1 =
The family sheet of WM Suite Hub 1.2.5, a corrected admin caption about the example videos and plural forms in eleven translations. No data or settings change.

= 1.3.0 =
Hub users: update WM Suite Hub to 1.2.0 together with this plugin. The hub contract moved to a new PHP namespace; until both are updated the plugin keeps working on its own but is not listed in the hub cockpit.
