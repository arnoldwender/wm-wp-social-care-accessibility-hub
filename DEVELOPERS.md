# WM Social Care Accessibility Hub — Developer Manual

> Everything below is measured against the code in this repository (v1.3.2, 2026-11-21).

---

## 1. Layout

| Path | Role |
| --- | --- |
| `wm-social-care-accessibility-hub.php` | Header, constants (`WM_SOCIAL_CARE_VERSION` / `_FILE` / `_PATH` / `_URL`), `require_once` of every class, singleton `Social_Care_Hub_Plugin`: `init` (text domain, post type, shortcodes, ecosystem bridges, REST, admin, `wm_social_care_ready` action), `plugins_loaded` priority 20 (hub registration), WP-CLI command. |
| `includes/Domain/Care_Needs_Checker.php` | Statutory SGB XI amounts, `STATUTORY_AS_OF`, `calculate_entitlements()` (filtered by `wm_social_care_bedarfscheck_result`), `entitlement_table()`. |
| `includes/Domain/Leichte_Sprache_Governor.php` | `apply_mediopunkt()` (15 fixed words); `audit_sentence_length()` and `validate_inclusion_europe_license()`. |
| `includes/Domain/Seed_Data_Manager.php` | `get_seed_data()`: the eight built-in examples (filtered by `wm_social_care_loesungswege_items`). |
| `includes/Domain/Video_Repository.php` | Post type `wm_loesungsweg` and four taxonomies; `sanitize_formula_injection()`. |
| `includes/Domain/Contrast_Governor.php` | WCAG relative luminance and contrast ratio; used by tests measuring frontend token pairs. |
| `includes/Contracts/Language_Level_Enum.php` | Three language levels (`standard`, `einfache_sprache`, `leichte_sprache`). |
| `includes/Contracts/WM_Social_Care_Module_Interface.php` | Slug, name and version, shared by the singleton and the adapter. |
| `includes/Frontend/Shortcodes.php` | Registration of the five shortcodes, the Bedarfscheck, the video library, the single player, the Easy Language card, the token sheet (`enqueue_frontend_tokens()`, `frontend_tokens()`). |
| `includes/Frontend/Accessibility_Toolbar.php` | `[wm_a11y_toolbar]` markup, styles and script. |
| `includes/Frontend/Accessible_Video_Player.php` | One example card with player, steps and Easy Language summary; card styles once per request. |
| `includes/Frontend/Ecosystem_Integrations.php` | WordPress 6.5+ Block Bindings source `wm-social-care/info` and GenerateBlocks 2.x dynamic tag `{{wm_social_care_info}}`. |
| `includes/API/REST_Controller.php` | `/loesungswege` and `/bedarfscheck` under `wm-social-care/v1` with `check_public_read_permission()` (HTTP `GET`/`HEAD` check + transient HMAC-SHA256 IP rate limiting). |
| `includes/CLI/Social_Care_CLI.php` | `wp wm-social-care seed` and `calc`. |
| `includes/Admin/Admin_Dashboard.php` | Menu, family sheet enqueue, the six measured rows, shortcode reference, and opt-in uninstall setting (`wm_social_care_delete_data_on_uninstall`). |
| `includes/Spoke/Social_Care_Spoke_Adapter.php` | WM Suite Hub contract; returns before the class declaration when the hub's interface does not exist. |
| `assets/css/wm-admin-tokens.css`, `wm-admin-ui.css` | Copies of the hub's canonical admin sheets, pinned by `tests/test-ui-family.php`. |
| `assets/css/wm-social-care-frontend.css` | Frontend tokens scoped to the component roots (section 3). |
| `languages/` | `wm-social-care-accessibility-hub.pot` and 28 compiled `.po`/`.mo` catalogs. |
| `uninstall.php` | Preserves all options and site content by default; removes `wm_social_care_delete_data_on_uninstall` and `wm_sc_rl_*` rate-limit transients only when explicitly opted in. |
| `bom.json` | CycloneDX 1.6 SBOM (`GPL-2.0-or-later`) with SHA-256 of `wm-social-care-accessibility-hub.php`. |
| `tests/` | `test-suite.php` (loads `test-social-care-hub.php`), `test-ui-family.php`, `test-pot-freshness.php`, `test-standalone-load.php`, `mutations.php` and `run-mutations.sh`, `wp-runtime-social-care.php` (inside WordPress), `stubs/`. |

Every PHP file under `includes/` exits before its first declaration when `ABSPATH` is not defined.

---

## 2. Load order and standalone operation

The hub's folder sorts after this plugin, so the adapter file loads the hub's contract from `WP_PLUGIN_DIR . '/wm-suite-hub/src/Contracts/WM_Plugin_Module_Interface.php'` when that file exists. If the interface still does not exist, the adapter file returns before declaring the class and the singleton keeps `null` instead of an adapter; `register_with_suite_hub()` then adds no filter. `tests/test-standalone-load.php` pins this in its own PHP process.

---

## 3. Frontend & Ecosystem Bridges

| Shortcode / Source | Callback | Output |
| --- | --- | --- |
| `wm_social_care_bedarfscheck` | `Shortcodes::render_bedarfscheck()` | Card with select, button and result list; element IDs from `wp_unique_id()`; inline script builds the result from `Care_Needs_Checker::entitlement_table()`. |
| `wm_leichte_sprache_card` | `Shortcodes::render_leichte_sprache_card()` | `<article>` with heading, pictogram (attribute or SVG), plain text with Mediopunkt, escaped, in paragraphs, fixed footer. |
| `wm_a11y_toolbar` | `Accessibility_Toolbar::render()` | `<aside>` with five buttons, styles and script. |
| `wm_videothek_loesungswege` | `Shortcodes::render_videothek()` | `<section>` headed "Beispiel-Lösungswege" with one player card per built-in example. |
| `wm_video_player` | `Shortcodes::render_single_player()` | One card for attribute `id` (1–8), empty string for any other ID. |
| `wm-social-care/info` | `Ecosystem_Integrations::resolve_block_binding()` | WordPress 6.5+ Block Bindings source (`summary`, `entlastungsbetrag_month`, `entlastungsbetrag_year`, `pflegegeld_month`, `sachleistung_month`, `verhinderungspflege_year`, `statutory_as_of`, `examples_count`). |
| `{{wm_social_care_info}}` | `Ecosystem_Integrations::render_generateblocks_tag()` | GenerateBlocks 2.x dynamic tag exposing the same statutory fields per `pflegegrad`. |

---

## 4. Developer Hooks

| Hook | Type | Parameters | Purpose |
| --- | --- | --- | --- |
| `wm_social_care_ready` | Action | `Social_Care_Hub_Plugin $plugin` | Fires at the end of `init_plugin()` once CPT, shortcodes, bindings and REST routes are registered. |
| `wm_social_care_bedarfscheck_result` | Filter | `array $result, int $pflegegrad, bool $include_verhinderung` | Filters the computed statutory entitlement row returned by `Care_Needs_Checker::calculate_entitlements()`. |
| `wm_social_care_loesungswege_items` | Filter | `array $items` | Filters the built-in example solution items returned by `Seed_Data_Manager::get_seed_data()`. |
| `wm_social_care_rate_limit_per_minute` | Filter | `int $max` | Filters the per-IP requests-per-minute threshold (default `60`) for `wm-social-care/v1` REST routes. |

---

## 5. REST

| Method | Route | Permission | Response |
| --- | --- | --- | --- |
| `GET` | `/wp-json/wm-social-care/v1/loesungswege` | `REST_Controller::check_public_read_permission` | `status`, `count` (8), `data` (the examples). |
| `GET` | `/wp-json/wm-social-care/v1/bedarfscheck` | `REST_Controller::check_public_read_permission` | `status`, `data` from `calculate_entitlements()`; argument `pflegegrad` integer 1–5, default 2, validated by `rest_validate_request_arg` and sanitized in the callback. |

`check_public_read_permission()` rejects non-`GET`/`HEAD` methods (`405`) and enforces a 60-second transient counter keyed by `wm_sc_rl_` + truncated `hash_hmac('sha256', $ip, wp_salt('auth'))` (`429` when exceeded).

---

## 6. Storage, uninstall and translations

Posts and terms of `wm_loesungsweg` are site content and always survive uninstalling. By default (`wm_social_care_delete_data_on_uninstall` = `'0'`), `uninstall.php` deletes nothing; when opted in (`'1'`), it removes `wm_social_care_delete_data_on_uninstall` and `wm_sc_rl_*` rate-limit transients. `languages/` holds `wm-social-care-accessibility-hub.pot` and 28 compiled `.po`/`.mo` catalogs.

---

## 7. Tests

```bash
php tests/test-suite.php             # entry point, loads test-social-care-hub.php; ends with "TEST SUITE RESULTS: N PASSED | M FAILED"
php tests/test-pot-freshness.php     # every gettext string in code is present in languages/wm-social-care-accessibility-hub.pot
php tests/test-standalone-load.php   # loads without the hub; run in its own process (test-suite.php also spawns it)
bash tests/run-mutations.sh          # each entry of tests/mutations.php undone once; the suite must fail every time
```

---

## 8. Security disclosure

See [SECURITY.md](SECURITY.md).
