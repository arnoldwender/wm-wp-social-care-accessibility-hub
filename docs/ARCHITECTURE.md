# Architecture — WM Social Care Accessibility & Kinästhetik Hub
`wm-social-care-accessibility-hub` · version `1.3.0` from the plugin header · WordPress 6.4+ · PHP 8.1+ · text domain `wm-social-care-accessibility-hub`

Measured against the code of release 1.3.0 (2026-11-20). Every figure below comes from the code, not from any document — the commands are given so they can be re-run.

```bash
# the inventory in this file
python3 the fleet's architecture inventory tool wm-social-care-accessibility-hub
```

---

## What runs where

```
five shortcodes (includes/Frontend/)
  Bedarfscheck          reads the statutory SGB XI amounts from Care_Needs_Checker
  Easy Language card    Mediopunkt rendering
  accessibility toolbar
  video library + single player   eight built-in examples

post type wm_loesungsweg + four taxonomies   (includes/Domain/)
two read-only GET routes under wm-social-care/v1 — check_public_read_permission (GET/HEAD + HMAC-SHA256 IP rate limit)
Block Bindings source wm-social-care/info + GenerateBlocks 2.x dynamic tag {{wm_social_care_info}}
developer hooks: wm_social_care_bedarfscheck_result, wm_social_care_loesungswege_items, wm_social_care_rate_limit_per_minute, wm_social_care_ready
wp wm-social-care calc | seed
admin screen (manage_options, opt-in wm_social_care_delete_data_on_uninstall) + hub adapter
```

**Preserves options and site content on uninstall by default** (`D-UNINSTALL`) and assesses no compliance.

---

## Wiring — what to touch for a given change

| Change | Files, in order | Trap |
|---|---|---|
| Change a statutory amount | **only** `Care_Needs_Checker`, with source and date | the shortcode, WP-CLI and REST all read that one table. An amount duplicated elsewhere is the classic two-sources bug |
| Add a shortcode | `includes/Frontend/` → register it → its tokens in `assets/css/wm-social-care-frontend.css` → add the painted colour pair to `test_frontend_tokens_defined_and_readable` | a callback that takes attributes must accept `array\|string`: **WordPress before 6.5 passes `''`** for a shortcode used without attributes |
| Add a REST route | `includes/API/` with a schema and `rest_validate_request_arg` | the two existing routes are public and read-only. Anything that writes needs a nonce **and** a capability |
| Touch the hub adapter | `includes/Spoke/` | the file **returns before declaring its class** when the hub interface is absent. Declaring a class that implements a missing interface is a fatal error on every request — `tests/test-standalone-load.php` exists for exactly this and must stay green in its own process |

---

## Fragile spots

1. **Never declare a class implementing a hub interface unless that interface exists.** A missing interface is a fatal on every request, not a degraded feature.
2. **Every CSS custom property used in `includes/Frontend/` is defined in `wm-social-care-frontend.css`**, scoped to the component root. A component that paints a background sets its text colour; text pairs measure ≥ 4.5:1 and borders, button boundaries and focus outlines ≥ 3:1. A new painted pair goes into the suite's measured list or it is not checked.
3. **Statutory amounts live in one place.** See the wiring table.
4. **Shortcode attribute callbacks accept `array|string`.**
5. **No feature is announced that no code implements, and no fixed success value.** Health, telemetry and CLI report what they measured.
6. **The examples carry no video since 2026-11-19**: the host they pointed at never resolved, and where videos would be hosted is open decision D8. An external host is not a pattern to copy.
7. **Never run `i18n-sync-fleet.py` on this plugin.** Use `wp-plugin-fleet-catalog-regenerate.sh`; the old `wm-social-care-a11y-*` catalogs wait for decision D10.
8. **Normative wording printed on every site**: the AnFöVO NRW line of the Bedarfscheck, the DIN SPEC 33429 footer and heading, and the eight examples' care and medication instructions, which carry no source. The wording stays as it is pending editorial review.

---

<!-- wm-measured-inventory -->

## Measured inventory

Regenerated from the code, never hand-edited. Everything above this line is written by hand and stays.

```bash
python3 the fleet's architecture inventory tool wm-social-care-accessibility-hub --append-to docs/ARCHITECTURE.md
```

## Inventory by layer

**17 PHP files, 2493 lines** (excluding `tests/`, `vendor/`, `node_modules/`, `languages/`).

| Layer | Files | Lines |
|---|---:|---:|
| `(raíz)` | 2 | 187 |
| `includes/API` | 1 | 170 |
| `includes/Admin` | 1 | 258 |
| `includes/CLI` | 1 | 85 |
| `includes/Contracts` | 2 | 49 |
| `includes/Domain` | 5 | 527 |
| `includes/Frontend` | 4 | 1013 |
| `includes/Spoke` | 1 | 204 |

---

## Classes

| Class | File |
|---|---|
| `Accessibility_Toolbar` | [`includes/Frontend/Accessibility_Toolbar.php`](../includes/Frontend/Accessibility_Toolbar.php) |
| `Accessible_Video_Player` | [`includes/Frontend/Accessible_Video_Player.php`](../includes/Frontend/Accessible_Video_Player.php) |
| `Admin_Dashboard` | [`includes/Admin/Admin_Dashboard.php`](../includes/Admin/Admin_Dashboard.php) |
| `Care_Needs_Checker` | [`includes/Domain/Care_Needs_Checker.php`](../includes/Domain/Care_Needs_Checker.php) |
| `Contrast_Governor` | [`includes/Domain/Contrast_Governor.php`](../includes/Domain/Contrast_Governor.php) |
| `Ecosystem_Integrations` | [`includes/Frontend/Ecosystem_Integrations.php`](../includes/Frontend/Ecosystem_Integrations.php) |
| `Leichte_Sprache_Governor` | [`includes/Domain/Leichte_Sprache_Governor.php`](../includes/Domain/Leichte_Sprache_Governor.php) |
| `REST_Controller` | [`includes/API/REST_Controller.php`](../includes/API/REST_Controller.php) |
| `Seed_Data_Manager` | [`includes/Domain/Seed_Data_Manager.php`](../includes/Domain/Seed_Data_Manager.php) |
| `Shortcodes` | [`includes/Frontend/Shortcodes.php`](../includes/Frontend/Shortcodes.php) |
| `Social_Care_CLI` | [`includes/CLI/Social_Care_CLI.php`](../includes/CLI/Social_Care_CLI.php) |
| `Social_Care_Hub_Plugin` | [`wm-social-care-accessibility-hub.php`](../wm-social-care-accessibility-hub.php) |
| `Social_Care_Spoke_Adapter` | [`includes/Spoke/Social_Care_Spoke_Adapter.php`](../includes/Spoke/Social_Care_Spoke_Adapter.php) |
| `Video_Repository` | [`includes/Domain/Video_Repository.php`](../includes/Domain/Video_Repository.php) |

---

## Entry points

**REST routes:** `/bedarfscheck`, `/loesungswege`

**WP-CLI:** `wm-social-care`

**Shortcodes:** `wm_a11y_toolbar`, `wm_leichte_sprache_card`, `wm_social_care_bedarfscheck`, `wm_video_player`, `wm_videothek_loesungswege`

---

## Persistence — who writes each key and who reads it

| Key | Written by | Read by |
|---|---|---|
| `wm_social_care_delete_data_on_uninstall` | `includes/Admin/Admin_Dashboard.php` | `includes/Admin/Admin_Dashboard.php`, `uninstall.php` |

Every key has exactly one writing layer and at least one reader.


---

## Gates

- `tests/mutations.php`
- `tests/run-mutations.sh`
- `tests/test-pot-freshness.php`
- `tests/test-social-care-hub.php`
- `tests/test-standalone-load.php`
- `tests/test-suite.php`
- `tests/test-ui-family.php`
- `tests/wp-runtime-social-care.php`
- `.github/workflows/ci.yml`

What CI actually runs is in the workflow files above — a test file that no workflow invokes is not a gate.

---

## Drift detected against the documentation

Nothing measurable: versions agree and every class named in the documents exists.

