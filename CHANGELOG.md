# Changelog — WM Social Care Accessibility Hub

All notable changes to **WM Social Care Accessibility Hub** are documented in this file in adherence to [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) and [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

### Fixed
- Deferred `Social_Care_Spoke_Adapter.php` loading strictly into `register_with_suite_hub()` when the hub contract is available.
- Fixed `Accessibility_Toolbar` multi-instance element ID collisions, single style/script rendering per page, WCAG 2.5.3 `aria-label` prefixes, and inline `role="status" aria-live="polite"` error feedback instead of `alert()`.

## [1.3.2] - 2026-11-21

### Fixed

- The 1.3.1 catalogs (the template and all 28 languages) named a mailbox as the address for reports about the source strings (`Report-Msgid-Bugs-To`). They name the plugin's support forum again, as 1.3.0 did and as every other WM Suite plugin does, and say 1.3.2 as their project version.

## [1.3.1] - 2026-11-21

### Changed

- The bundled family stylesheet is identical to WM Suite Hub 1.2.5 again: it starts the admin header below WordPress's help toggle instead of shortening it. This plugin's own screens register no help tab, so they change only where another plugin adds one.
- The catalogs carry the Author URI as `https://arnoldwender.com`, as the plugin header does.

- The first screenshot is re-shot with the corrected caption, and its description in `readme.txt` and the alt texts in the three READMEs list what the screen shows instead of "managing Easy-Language guides and care solution directories".

### Fixed

- The admin screen said the example videos point at an external server, and its shortcode reference called `[wm_video_player]` an HTML5 player. None of the eight built-in examples has carried a video since 1.2.3; the caption now says so and the reference names the steps and the Easy Language summary the player shows. The quick start in the three READMEs said the example videos "currently do not load"; it now says the examples carry none. All 28 catalogs carry the two new strings.
- In the eleven languages with more than two plural forms (Arabic, Croatian, Czech, Irish, Latvian, Lithuanian, Maltese, Polish, Romanian, Slovak, Slovenian), the admin line that counts published Lösungswege used one form for every count above one. Each form now matches its count; Irish uses a label form ("Lösungswege foilsithe: %d"), Arabic counts the entries as منشور.

## [1.3.0] - 2026-11-20

### Changed
- The WM Suite Hub contract now lives in the `ArnoldWender\SuiteHub\Contracts` namespace. The spoke adapter registers only with WM Suite Hub 1.2.0 or later, so update the hub together with this plugin; used on its own, the plugin works as before.
- Renamed the plugin's own namespace to `ArnoldWender\SocialCareA11y\…` (same vendor as the hub).
- `readme.txt`, `CLAUDE.md` and `docs/ARCHITECTURE.md` no longer say that five examples link an external video: none carries one since 1.2.3.
- The `.wordpress-org/` banners show the current version.

### Removed
- The archived pre-2026-11-19 marketing visuals in `versionen/`: they claimed features and conformity the plugin does not have. They were never part of the installable package.

## [1.2.3] - 2026-11-19

- Removed the agency name, links to unrelated projects and the personal mailbox from code, catalogs and documentation; the admin family badge and the hub menu read "WM Suite". The READMEs say the plugin was built with an agentic workflow (AI coding agents) and should be reviewed like any third-party code.

- Added root `SUPPORT.md`, `.github/ISSUE_TEMPLATE/{config.yml,bug_report.yml}`, and explicit zero-support / "AS IS" liability notices across `README.md`, `README.de.md`, `README.es.md`, `readme.txt`, `SECURITY.md`, and `llms.txt`.
- Migrated plugin author/catalog/repository metadata to personal open-source distribution (`Arnold Wender`, `https://arnoldwender.com/wordpress-plugins/wm-social-care-accessibility-hub/`, `https://github.com/arnoldwender/wm-wp-social-care-accessibility-hub`), normalized FSF GPL-2.0 `LICENSE` metadata where applicable, and updated `.wordpress-org/` banner branding to `ARNOLD WENDER · OPEN SOURCE`.

## [Unreleased]

## [1.2.2] - 2026-11-19

### Added
- Official `.wordpress-org/` directory visual kit (`icon.svg`, `icon-128x128.png`, `icon-256x256.png`, `banner-772x250.png`, `banner-1544x500.png`) and live Playwright screenshots (`screenshot-1.png` through `screenshot-2.png`) with `== Screenshots ==` in `readme.txt`.
- Visual previews and direct link to `https://arnoldwender.com/wordpress-plugins/wm-social-care-accessibility-hub/` in `README.md`, `README.de.md`, `README.es.md`, and `llms.txt`.

### Changed
- Archived superseded early mockups and banners non-destructively in `versionen/visuals-pre-2026-11-19/` (keeping `docs/screenshots/design-family/` intact) and excluded `versionen/` from distribution packages via `.distignore`.
- Updated release metadata, SBOM (`bom.json`), `DEVELOPERS.md`, `SECURITY.md`, and stale `README.es.md` version text to `1.2.2`.

---

## [1.2.1] - 2026-11-19

### Changed
- Point `Plugin URI` to `https://arnoldwender.com/wordpress-plugins/wm-social-care-accessibility-hub/` and align release metadata at `v1.2.1`.

---

## [1.2.0] - 2026-11-16

### Added
- **REST Rate Limiting & Read-Only Guard (`SEC-02`):** Replaced raw `'__return_true'` permission callbacks on `/wp-json/wm-social-care/v1/loesungswege` and `/bedarfscheck` with `REST_Controller::check_public_read_permission()`, enforcing HTTP `GET`/`HEAD`, transient-based `hash_hmac('sha256', ...)` client IP rate limiting (`wm_sc_rl_*`, default 60 requests/minute), and strict `pflegegrad` handler sanitization.
- **Opt-In Uninstall Preservation (`D-UNINSTALL`):** Updated `uninstall.php` and `Admin_Dashboard` so deleting the plugin preserves all options and site content by default unless an administrator explicitly enables `wm_social_care_delete_data_on_uninstall` (default `'0'`).
- **Developer Hooks & Ecosystem Bridges:** Added `do_action('wm_social_care_ready', $plugin)` on `init`, filters `wm_social_care_bedarfscheck_result`, `wm_social_care_loesungswege_items`, and `wm_social_care_rate_limit_per_minute`, WordPress 6.5+ Block Bindings source `wm-social-care/info`, and GenerateBlocks 2.x dynamic tag `{{wm_social_care_info}}` (`Ecosystem_Integrations`).
- **28 Compiled Gettext Catalogs (`D-LOCALES`):** Regenerated `languages/wm-social-care-accessibility-hub.pot` from source and compiled 28 complete `.po`/`.mo` catalogs across all 24 official EU languages plus `ar`, `en_US`, `fi`, and `tr_TR`; removed unused legacy `wm-social-care-a11y-*` files.
- **Release & Distribution Hygiene:** Added CycloneDX 1.6 `bom.json`, `.license-allowlist.yml`, WordPress Playground `.wordpress-org/blueprints/blueprint.json`, symlinked `AGENTS.md -> CLAUDE.md`, and synchronized `README.md`, `README.de.md`, `README.es.md`, and `docs/translations/`.

### Added — the catalog freshness gate (2026-09-20)

- `tests/test-pot-freshness.php` and two CI steps on PHP 8.3: the gate extracts the translatable strings from the source and fails if any of them lacks an entry in the `.pot`. A string with no catalog entry cannot be translated by anyone into any language — the translator never sees it, nothing reports an error, and the interface simply stays German.
- **This catalog was already current** — 87 translatable strings, 87 entries, nothing missing and nothing stale — and that is the reason to add the gate rather than a reason to skip it. Fifteen of the fleet's twenty-four plugins had drifted, one of them down to a single entry still shared with the code, and not one of them had a check that could notice. Being correct today is not a mechanism.
- The gate takes its definition of "fresh" from `wp i18n make-pot` rather than a regex of its own, reads the text domain from the plugin header instead of holding a second copy, treats 0 extracted strings as a failure rather than a pass, ignores the plugin-header fields `make-pot` adds (nobody translates an author name or a URL), requires wp-cli with no escape hatch, and runs its own self-test before judging the plugin.
### Family sheets (2026-09-16)
- Re-synced from the hub: the badge variants carry the state tokens on body surfaces, where the command-banner inks measured 1.49:1 to 2.21:1 against the composed background (hub PR #19), and the canonical sheet dropped five classes that belonged to a single plugin (hub PR #21). `tests/test-ui-family.php` re-pins both md5s.

### Fixed (2026-09-15, reader pass)

Found by a reader pass that compared the documents with the code, then measured on both staging sites (a local WordPress install on PHP 8.3 and a hosted WordPress staging environment on PHP 8.4) before anything changed. Each fix has an assertion in `tests/test-social-care-hub.php` that fails without it and an entry in `tests/mutations.php`.

- **Four shortcodes were fatal on WordPress before 6.5.** A shortcode without attributes receives `''` there, and `render_bedarfscheck()`, `render_videothek()`, `render_single_player()` and `render_leichte_sprache_card()` typed it `array` (TypeError, measured on both sites by passing `''`). The header admits WordPress 6.4; the callbacks accept `array|string`.
- **The Easy Language card printed markup as text.** WordPress runs `wpautop()` on the enclosed text before the shortcode, and the card escaped the `<p>` and `<br />` it found, so readers saw "&lt;br /&gt;" and "&lt;/p&gt;" (4 escaped tags for two classic paragraphs, 1 in the shortcode block). The markup becomes line breaks before escaping.
- **Two checks on one page shared their element IDs**, so the second one updated the first, and the select pointed `aria-describedby` at the whole result region, which was also announced when the script filled it on load. IDs come from `wp_unique_id()`, the select has no description reference, and the region becomes a status after the first fill.
- **The library called eight built-in examples a "LösungsWege-Datenbank (LWD)" of "Barrierefreie" guides** and printed the card styles once per card (9 style blocks). There is no database and nothing assesses accessibility: the heading is "Beispiel-Lösungswege" and the card styles go out once per page.
- **Read aloud read the toolbar first and could stay on "Stopp".** Placed in the content, the toolbar is inside `<main>`, so "A-", "A", "A+", "Kontrast" and "Vorlesen" came before the page text; a failed synthesis never reset the button; the voice was German on every page. The toolbar is hidden during the read, `onerror` resets the button, and the voice follows the page's `lang`.
- **High contrast hid the state of the toolbar buttons**: every element got the same yellow on black with `!important`. The pressed contrast button and the speaking button invert the two colours.
- **Control boundaries below 3:1.** The select border stood at 1.48:1 on the white card, the check button's focus outline at 1.67:1, the toolbar button border at 1.72:1 against the toolbar. They use `slate-600`, `primary-800` and the new `slate-400`; the suite now measures 19 text pairs (was 11) and 6 boundaries and outlines, and checks that each rule uses the measured token.
- **Nothing on the front end was translatable**: 0 gettext calls while the shortcodes rendered (measured on both sites). The frontend strings go through gettext; the script strings reach the inline scripts as JSON.
- **The admin screen did not measure two of the five shortcodes.** With `wm_videothek_loesungswege` and `wm_video_player` unregistered it still showed every row as registered. It has a row for each shortcode and the post type.
- **`wp wm-social-care calc` accepted fractions and printed a zero budget.** "2.9" became care degree 2 and care degree 1 printed "Bis zu 0.00 € / Jahr"; amounts used a decimal point. Only whole numbers 1–5 are accepted, the joint budget line starts at care degree 2 as in the check, and amounts print as "131,00 €".
- **Seven files had no direct-access guard** (`Language_Level_Enum`, `WM_Social_Care_Module_Interface`, `Care_Needs_Checker`, `Contrast_Governor`, `Leichte_Sprache_Governor`, `Seed_Data_Manager`, `Video_Repository`), while `CLAUDE.md` and `CONTRIBUTING.md` said every file had one. All exit before their declaration outside WordPress.
- **The token sheet arrived after the component outside singular content.** In a widget or template part the sheet was enqueued after the head and printed in the footer, so the component first painted with undefined custom properties. After `wp_head` the first component prints the sheet inline once.
- **The catalogs could not translate the code.** The template held 13 strings plus 6 double-encoded copies and none of the 28 admin strings; every `.mo` differed from its `.po` and the de_DE catalog read as 0 entries. Template and the 25 catalogs of the text domain are generated from the code; the old `wm-social-care-a11y` set is untouched (D10).

### Changed (2026-09-15)

- The suite runs each test method isolated and ends with the fleet results line; CI adds a PHP syntax step, requires that line with 0 failures, runs `tests/run-mutations.sh`, and drops the SBOM step that could only warn.
- `tests/wp-runtime-social-care.php` checks the shortcodes through WordPress' own content functions, the admin rows, the compiled catalog, REST, WP-CLI and the adapter inside WordPress, and confirms that no option was written.
- `assets/images/`, `README.de.md`, `README.es.md` and `DEVELOPERS.md` are left out of the plugin package. The README no longer embeds the banner ("BFSG 2025 COMPLIANT", "100% Zero-CDN Sovereignty") and no longer points at the admin screenshots of the 2026-09-14 redesign.

### Fixed (2026-09-14, honest docs and runtime pass)
- **Fatal error without WM Suite Hub.** `Social_Care_Spoke_Adapter` declared `implements WM_Plugin_Module_Interface` unconditionally and loaded that interface only when `wm-suite-hub` was on disk, so on a site without the hub every request ended in "Interface not found" (reproduced 2026-09-14 with an empty plugin directory). The adapter file now returns before the class declaration, the plugin singleton keeps no adapter and registers no hub filter; `tests/test-standalone-load.php` runs the load in its own process. The unit suite had hidden this because it declares a stub of the interface.
- **Hub adapter measured instead of fixed.** Health was `HEALTHY`, telemetry `bfsg_status: COMPLIANT`, `leichte_sprache: ACTIVE` and `kinaesthetik_vids: 24` whatever the site, every action name answered "executed successfully", the dependencies named two plugins the code never uses, and `get_sbom_data()` returned a whole CycloneDX document that the hub merged into a component. Health and `self_test` now check the five shortcodes and the post type (`DEGRADED` before `init`), telemetry reports registrations, the eight examples, the host of their videos, published posts and the as-of date of the amounts, unknown actions fail, dependencies are empty and the SBOM is one component. The hub filter is `wm_register_suite_module`. `is_bfsg_compliant()` (a fixed `true` nothing read) and `get_sbom_package()` left the plugin's own interface.
- **Unreadable frontend.** None of the CSS custom properties the shortcodes use was defined outside wp-admin. Measured on GeneratePress: the Bedarfscheck button was white text without a background, card texts inherited the theme colour (light grey on the white card) and paddings, borders and radii were zero. `assets/css/wm-social-care-frontend.css` defines them on the component roots and is enqueued for posts that use a styled shortcode and from each render; cards set their own text colour; the video library heading inherits the theme colour; remaining pixel values became tokens. Tests measure every text pair against 4.5:1 with `Contrast_Governor`; the primary colour is the family accent `#0369a1` (5.93:1 under white text). — *no screenshot or log of the GeneratePress measurement was kept; the suite listed 11 pairs, not every pair, until 2026-09-15*
- **Toolbar.** The "Leichte Sprache" button had no handler and is removed; the contrast toggle exposes `aria-pressed`; the read-aloud label changes through `textContent` instead of `innerHTML`; emoji replaced by inline SVG.
- **Player and library.** A hidden live region announced keys (M, C, arrows) no script implements and is removed; the duration reads "Dauer: 3:45" instead of "3:45 Minuten"; emoji removed from headings and from the default Easy Language pictogram (now an SVG); the card is an `<article>`; `[wm_video_player]` with an unknown ID renders nothing instead of example 1.
- **REST `/bedarfscheck`** validates `pflegegrad` as integer 1–5 (400) instead of answering care degree 2 for any other value, and no longer reads `$_GET` behind the request.
- **`wp wm-social-care seed`** printed "Erfolgreich 8 Lösungswege-Datensätze geladen." without writing anything; it now lists the examples and says that nothing was written.
- **`uninstall.php`** deleted an option and a transient no version ever wrote (git log -S); it now removes nothing and says why.
- **Admin screen** labels no longer pair registrations with norms (BFSG 2025, DIN SPEC 33429, BGG § 11); the library is described without the filters it never had and the post-type row says that the shortcodes show the built-in examples, not the posts.
- Plugin header description measured against the code.
- Tests: 102 in `tests/test-suite.php` (adapter health and telemetry, frontend tokens and contrast, dead buttons, emoji, unknown IDs, REST schema, CLI output, uninstall orphans, standalone load), each fix mutation-checked against `main`. The language test pinned the catalogs of the former domain `wm-social-care-a11y`; it now checks the files of the loaded domain. — *no mutation list was kept; `tests/mutations.php` and `tests/run-mutations.sh` exist since 2026-09-15*

### Documentation (2026-09-14)
- README (EN/DE/ES), DEVELOPERS, readme.txt, CLAUDE.md/AGENTS.md, llms.txt, CONTRIBUTING, SECURITY and the 24 translated READMEs rewritten from the code. They described `[wm_care_calculator]`, `[wm_care_leichte_sprache]`, `[wm_pflege_notruf]`, `wp care status|verify|export`, `wp social-care`, REST `/status` `/submit` `/export` under `wm-care/v1`, the table `wp_wm_care_calculator_sessions`, the filter `wm_care_entitlement_rates`, GoBD audit reports, an HMAC hash chain, CSV exports, a 66-character line limit, 125 € and full compliance with BITV 2.0, BFSG and WCAG 2.2 AAA; none of those exist in the code, they were never shipped.
- Open for Arnold: the AnFöVO NRW line of the Bedarfscheck and the DIN SPEC 33429 footer of the Easy Language card are printed on every site; the example videos point at a host that does not resolve (D8).

### Design family (hackaton phase 3, 2026-09-14)
- The admin screen renders from the two WM Suite family sheets (`assets/css/wm-admin-tokens.css`, `assets/css/wm-admin-ui.css`, byte-identical copies of the hub's canonical files, pinned by `tests/test-ui-family.php`); the plugin carries no admin sheet of its own. Before this nothing was enqueued: the screen used WordPress' `.card` plus inline styles that referenced custom properties no stylesheet defined (`--space-4`, `--color-emerald-600`), so those declarations were invalid. Native `.wrap.wm-admin`, family header with the hub/standalone badge, family menu icon, gettext titles and strings (the screen was untranslatable), no emoji.
- **The checklist measures instead of asserting:** it printed four hardcoded ticks ("Aktiviert", "Bereit", "Integriert", "100% Lokal") that nothing could change. Each row now reports whether the shortcode or post type is registered on this site, and the video library row counts the published Lösungswege. The shortcode reference lists the five shortcodes the code registers (`wm_social_care_bedarfscheck`, `wm_leichte_sprache_card`, `wm_a11y_toolbar`, `wm_videothek_loesungswege`, `wm_video_player`). — *the rows measured three of the five shortcodes and the post type; the counting row was the post-type row; the video shortcodes got their rows on 2026-09-15*
- The main file defines the constants `CLAUDE.md` named (`WM_SOCIAL_CARE_VERSION`, `_FILE`, `_PATH`, `_URL`); until now it defined none.

### Statutory amounts (2026-09-14)
- **The care-benefit calculator carried the 2024 figures.** `Care_Needs_Checker` returned 125 € Entlastungsbetrag, 332 / 573 / 765 / 947 € Pflegegeld, 761 / 1.432 / 1.778 / 2.200 € Pflegesachleistung and a separate 1.612 € Verhinderungspflege budget; the shortcode's script carried its own hand-typed copy of the same numbers and printed "1.500 € / Jahr". Since 2025-01-01 (Pflegeunterstützungs- und -entlastungsgesetz, +4,5 %) the amounts are 131 € (§ 45b), 347 / 599 / 800 / 990 € (§ 37), 796 / 1.497 / 1.859 / 2.299 € (§ 36), and since 2025-07-01 Verhinderungs- and Kurzzeitpflege share one annual budget of 3.539 € (§ 42a); all unchanged for 2026, next regular adjustment not before 2028. Verified 2026-09-14 against pflege.de (Entlastungsbetrag, Pflegegeld, gemeinsamer Jahresbetrag), pflegeabc.de (Pflegesachleistungen) and dejure.org (§ 45b SGB XI). The 1.0.0 entry below already said 131 €; the code never did.
- One source: the shortcode embeds `Care_Needs_Checker::entitlement_table()` (JSON) instead of its own table, WP-CLI prints the Sachleistung and the as-of date, results carry `statutory_as_of`. Tests pin the 2025/2026 figures, the single source and the absence of the 2024 values in the rendered shortcode.

### Fixed
- **Text domain equals the plugin slug.** The header declared `wm-social-care-a11y` and 16 strings used it while `languages/` already carried a full `wm-social-care-accessibility-hub-*` catalog set next to the old one; `CLAUDE.md` named the slug as the domain. WordPress loads catalogs by the header's domain, so the slug-named set was never read. Header and calls now use `wm-social-care-accessibility-hub`; the old `wm-social-care-a11y-*` catalogs stay in place until the translation decision (D10) is taken.
- Plugin Check (dist view, 2026-09-14): 27 → 0 errors. The video player escapes values where it prints them instead of once at the top; the exception in the contrast governor escapes the offending colour; the spoke adapter carries the direct-access guard; `readme.txt` added (Stable tag 1.1.0, Tested up to 7.1 from today's runtime pass) and `Tested up to` in the header. — *no report of that run was kept; the Plugin Check and runtime results of 2026-09-15 are in the pull request of that day*

---

## [1.1.0] — 2026-08-29

### Added
- **24 Official European Union Languages:** Full binary `.mo` and source `.po` translation packages in `languages/` (`bg_BG`, `cs_CZ`, `da_DK`, `de_DE`, `el_GR`, `en_GB`, `es_ES`, `et_EE`, `fi_FI`, `fr_FR`, `ga_IE`, `hr_HR`, `hu_HU`, `it_IT`, `lt_LT`, `lv_LV`, `mt_MT`, `nl_NL`, `pl_PL`, `pt_PT`, `ro_RO`, `sk_SK`, `sl_SI`, `sv_SE`).
- **Developer Documentation Suite:** Created `DEVELOPERS.md` with complete architecture diagrams, hook references, database schema, and REST API guides. — *never shipped: the hooks, schema and routes it described did not exist; rewritten 2026-09-14*
- **Decoupled REST API Controller:** Registered namespace `/wp-json/wm-social-care/v1` with strict nonce and permission validation. — *the two GET routes are public and never checked a nonce*
- **Zero-CDN Frontend Shortcode:** Implemented `[wm_pflege_notruf]` with inline SVG icons and CSS design tokens. — *never shipped*
- **CycloneDX v1.6 SBOM Engine:** Dynamic SHA-256 entry file integrity hashing and spoke adapter integration for **WM Suite Hub**. — *no hash was computed until 2026-09-14*
- **Enterprise Showcase Assets:** High-fidelity 16:9 marketing banner and cockpit showcase mockups conditioned on real screenshot topology. — *the banner prints compliance badges and the mockup shows screens, validation dates and an amount of 125 € the plugin does not have*
- **Clean Uninstallation Lifecycle:** Comprehensive `uninstall.php` handler for clean database and transient cache purging. — *it deleted two names nothing wrote*

### Security
- **CWE-1236 Mitigation:** Active sanitization against formula injection attacks on all CSV exports and dynamic text inputs. — *never shipped: there is no export and the helper has no caller*
- **Constant-Time Cryptography:** Enforced `hash_equals()` verification on all HMAC-SHA256 signature chains. — *never shipped: the plugin has no signature chain*
- **SQL Parameterization:** Refactored all queries to use strict `$wpdb->prepare()` with parameterized placeholders. — *never shipped: the plugin runs no SQL of its own*

### Quality & Tests
- Expanded standalone deterministic test suite (`tests/test-suite.php`) with 100% green pass rate and zero external dependencies. — *the suite depended on the sibling repository wm-suite-hub and could not pass in a clean checkout until 2026-08-31 (commit 1c29a18)*

---

## [1.0.0] — 2026-08-25

### Initial Enterprise Release
- Core architectural implementation for Healthcare & Social Care. — *the first commit of this repository is dated 2026-08-29*
- WP-CLI management commands under `wp social-care`. — *never shipped: the command is `wp wm-social-care`*
- Revisionssicheres GoBD cryptographic ledger and telemetry hooks. — *never shipped*
- Strict compliance with SGB XI § 45a/b (Entlastungsbetrag 131 €/Monat), AnFöVO NRW, BFSG 2025 & WCAG 2.2 AAA. — *never shipped: the code carried 125 € until 2026-09-14 and assesses no compliance*
