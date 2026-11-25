# WM Social Care Accessibility Hub (Gaeilge)

<p align="center">
  [🇧🇬 Български](README.bg.md) • [🇨🇿 Čeština](README.cs.md) • [🇩🇰 Dansk](README.da.md) • [🇩🇪 Deutsch](../../README.de.md) • [🇬🇷 Ελληνικά](README.el.md) • [🇬🇧 English](../../README.md) • [🇪🇸 Español](../../README.es.md) • [🇪🇪 Eesti](README.et.md) • [🇫🇮 Suomi](README.fi.md) • [🇫🇷 Français](README.fr.md) • [🇮🇪 Gaeilge](README.ga.md) • [🇭🇷 Hrvatski](README.hr.md)<br/>[🇭🇺 Magyar](README.hu.md) • [🇮🇹 Italiano](README.it.md) • [🇱🇹 Lietuvių](README.lt.md) • [🇱🇻 Latviešu](README.lv.md) • [🇲🇹 Malti](README.mt.md) • [🇳🇱 Nederlands](README.nl.md) • [🇵🇱 Polski](README.pl.md) • [🇵🇹 Português](README.pt.md) • [🇷🇴 Română](README.ro.md) • [🇸🇰 Slovenčina](README.sk.md) • [🇸🇮 Slovenščina](README.sl.md) • [🇸🇪 Svenska](README.sv.md)
</p>

---

<p align="center">
  <a href="https://wordpress.org"><img src="https://img.shields.io/badge/WordPress-6.4%20--%207.1+-21759b.svg?logo=wordpress&logoColor=white" alt="WordPress Version" /></a>
  <a href="https://php.net"><img src="https://img.shields.io/badge/PHP-8.1%20%7C%208.2%20%7C%208.3+-777bb4.svg?logo=php&logoColor=white" alt="PHP Version" /></a>
  <img src="https://img.shields.io/badge/Language-ga_IE-blue.svg" alt="Gaeilge" />
  <img src="https://img.shields.io/badge/Zero--CDN-100%25%20Local-purple.svg" alt="Zero-CDN" />
  <a href="../../LICENSE"><img src="https://img.shields.io/badge/License-GPLv2%20or%20later-brightgreen.svg" alt="License" /></a>
</p>

> **Five shortcodes for care and support websites — a care-benefit check with the statutory SGB XI amounts, an Easy Language (Leichte Sprache) card, an accessibility toolbar and a video library of eight built-in example guides — plus a "Lösungsweg" post type, Block Bindings and GenerateBlocks dynamic tags; it does not check or make a site accessible**  

---

## 📸 Gaeilge Enterprise Showcase

!WM Social Care Accessibility Hub Banner

![WM Social Care Accessibility Hub Dashboard UI](../../.wordpress-org/screenshot-1.png)

---

## 🟢 LEIBHÉAL 1: TREOIR THAPA DO THOSNAITHEOIRÍ AGUS CRUTHAITHEOIRÍ SUÍOMH (NO-CODE)

### Cad a dhéanann an breiseán seo do do shuíomh gréasáin?
Five shortcodes for care and support websites — a care-benefit check with the statutory SGB XI amounts, an Easy Language (Leichte Sprache) card, an accessibility toolbar and a video library of eight built-in example guides — plus a "Lösungsweg" post type, Block Bindings and GenerateBlocks dynamic tags; it does not check or make a site accessible

### 🚀 Conas tús a chur leis i 2 nóiméad (Céim ar Chéim):
1. Install and activate the plugin (WordPress 6.4+, PHP 8.1+). Activation creates no custom tables and writes no options until you save the optional uninstall setting.
2. Put `[wm_social_care_bedarfscheck]` on a page about care benefits and check the NRW line against your situation first (Known issues).
3. Put `[wm_leichte_sprache_card title="Information in Leichter Sprache"]…[/wm_leichte_sprache_card]` around a text that is already written in Easy Language.
4. Put `[wm_a11y_toolbar]` at the top of the page content where visitors should find it.
5. Before using `[wm_videothek_loesungswege]` or `[wm_video_player]`, open the page: the built-in examples carry no video; they show their steps and an Easy Language summary (Known issues).

### 💡 Ceisteanna Coitianta do Thosaitheoirí:
#### ❓ What is not included?
No check of a site's pages for accessibility and no assessment of whether a site meets BFSG, BITV 2.0, WCAG or any other requirement; no automatic rewriting into Easy Language (only the Mediopunkt on 15 fixed words); no sign-language videos of its own; no display of `wm_loesungsweg` posts and no filters in the video library; no visitor session storage, export or audit report; no individual benefit calculation or legal advice (the check shows statutory maximum amounts).

---

## 🟡 LEIBHÉAL 2: TREOIR DO MHOISTIR GHRÉASÁIN AGUS RIARTHÓIRÍ

### What the plugin does
1. **Care-benefit check.** `[wm_social_care_bedarfscheck]` shows a select for care degrees 1–5 and, for the chosen degree, the Entlastungsbetrag (§ 45b SGB XI, 131 € a month, 1,572 € a year), the Pflegegeld (§ 37: 0 / 347 / 599 / 800 / 990 €) and, from care degree 2, the joint annual budget for Verhinderungs- and Kurzzeitpflege (§ 42a, 3,539 €), with the line "Stand der Beträge: 2025-07-01 (gilt unverändert 2026)". The amounts come from one table in `Care_Needs_Checker`, which WP-CLI, REST and Block Bindings also use and which additionally holds the Pflegesachleistung (§ 36: 796 / 1,497 / 1,859 / 2,299 €); they were checked on 2026-09-14 against pflege.de, pflegeabc.de and dejure.org. The result is rendered in the browser; nothing is sent or stored. Each check gets its own element IDs, so several can sit on one page, and its result region announces a change of care degree but not the result filled in on page load. The card also prints "Anerkennung NRW: Voll abrechenbar über alle Pflegekassen nach AnFöVO." for every site (see Known issues).
2. **Easy Language card.** `[wm_leichte_sprache_card title="…" pictogram="…"]text[/wm_leichte_sprache_card]` prints the enclosed text escaped, in paragraphs, inside an `<article>` with the title as heading, an information icon (or the `pictogram` text), and the footer "Text nach den Regeln der Leichten Sprache (DIN SPEC 33429)." Paragraph and line-break markup that WordPress adds to the enclosed text (in classic content and in the shortcode block) is turned back into paragraphs first, so no tag shows as text. It inserts a Mediopunkt (·) into 15 compound care words from a fixed list (for example Pflege·kasse, Entlastungs·betrag). It does not rewrite or check the text.
3. **Accessibility toolbar.** `[wm_a11y_toolbar]` prints five buttons: text size down, reset and up (120 % and 140 % on `<html>`), high contrast (yellow on black on every element, state in `aria-pressed`; the pressed contrast button and the speaking read-aloud button invert those colours) and read aloud (the browser's speech synthesis reads the first 3,000 characters of `<main>`, or `<body>`, without the toolbar's own labels, in the language of the page, German when the page declares none). Nothing is stored; a reload resets it.
4. **Video library and single player.** `[wm_videothek_loesungswege]` lists, under the heading "Beispiel-Lösungswege", the eight built-in examples of `Seed_Data_Manager` (transfer from wheelchair to bed, kitchen orientation with low vision, a sign-language app, a rollator for rough ground, tactile orientation at home, tying shoes one-handed, voice-controlled smart home, tactile pill sorting). `[wm_video_player id="1"]` shows one of them (IDs 1–8; an unknown ID renders nothing). Each card has the title, a duration badge when the example has one, a native HTML5 `<video>` with a German captions track when the example has a video, the numbered steps and a summary in Easy Language. The card styles are printed once per page.
5. **Content model.** Public post type `wm_loesungsweg` (archive `/loesungswege/`, REST `/wp/v2/loesungswege`) with the taxonomies `wm_kategorie` (hierarchical), `wm_zielgruppe`, `wm_hilfsmittel` and `wm_pflegegrad` (hierarchical). No shortcode and no plugin route reads these posts.
6. **REST.** `GET /wp-json/wm-social-care/v1/loesungswege` returns the eight examples; `GET /wp-json/wm-social-care/v1/bedarfscheck?pflegegrad=1..5` returns the amounts for one care degree (default 2; any other value answers 400). Both are read-only and guarded by `REST_Controller::check_public_read_permission()`, which enforces `GET`/`HEAD`, hashes client IPs with `hash_hmac('sha256', ...)` so raw IPs are never stored, and applies a 60-requests-per-minute transient rate limit (`wm_sc_rl_*`).
7. **WP-CLI.** `wp wm-social-care calc --pflegegrad=<1-5>` prints the amounts in German number format (the joint annual budget from care degree 2, as the check does) and ends with an error for anything but a whole number from 1 to 5; `wp wm-social-care seed` lists the eight examples and writes nothing.
8. **Admin & opt-in uninstallation.** Menu *Social Care A11y* (position 30), page `wm-social-care-accessibility-hub`: whether each of the five shortcodes and the post type is registered on this site (six measured rows), how many Lösungswege are published, the shortcode reference, and an opt-in checkbox (`wm_social_care_delete_data_on_uninstall`, default `'0'`) controlling whether `uninstall.php` deletes plugin options and rate-limit transients upon deletion. By default `uninstall.php` preserves all options and site content.
9. **Ecosystem bridges & developer hooks.** Registers WordPress 6.5+ Block Bindings source `wm-social-care/info` and GenerateBlocks 2.x dynamic tag `{{wm_social_care_info}}` exposing statutory SGB XI amounts, as-of date, and example counts; fires `do_action('wm_social_care_ready', $plugin)` on `init` and provides filters `wm_social_care_bedarfscheck_result`, `wm_social_care_loesungswege_items`, and `wm_social_care_rate_limit_per_minute`.
10. **WM Suite Hub spoke.** When `wm-suite-hub` is installed, `includes/Spoke/Social_Care_Spoke_Adapter.php` registers through `wm_register_suite_module`: health `HEALTHY` with the five shortcodes and the post type registered, `DEGRADED` when some are missing or before `init`, `ERROR` when none is; telemetry with those registrations, the example count, the host of the example videos, the published post count and the as-of date of the amounts; action `self_test`; one SBOM component. Without the hub the plugin runs on its own.
11. **Front end & translations.** The visible source strings are German and go through gettext (text domain `wm-social-care-accessibility-hub`) with 28 compiled `.po`/`.mo` catalogs in `languages/` (all 24 official EU languages plus `ar`, `en_US`, `fi`, `tr_TR`). The token sheet `assets/css/wm-social-care-frontend.css` loads in the head on pages whose content uses a styled shortcode; a shortcode that renders after the head (a widget, a template part) prints the sheet inline once, in front of its markup. Text pairs measure at least 4.5:1; field borders, button boundaries and focus outlines at least 3:1.

### Not shipped
No check of a site's pages for accessibility and no assessment of whether a site meets BFSG, BITV 2.0, WCAG or any other requirement; no automatic rewriting into Easy Language (only the Mediopunkt on 15 fixed words); no sign-language videos of its own; no display of `wm_loesungsweg` posts and no filters in the video library; no visitor session storage, export or audit report; no individual benefit calculation or legal advice (the check shows statutory maximum amounts).

---

## 🔴 LEIBHÉAL 3: TREOIR D'FHORBRÓIRÍ AGUS AILTRÍ BOGEARRAÍ

Classes, hooks, options, the admin screen and the WM Suite Hub adapter are documented in the English manual: **[DEVELOPERS.md](../../DEVELOPERS.md)**.

---

## ⚖️ Comhlíonadh Dlí agus Rialacháin an AE

- **What the plugin provides:** building blocks a care provider can place on pages. It does not audit the site, does not make it accessible and does not assess whether the site, its texts or its benefit information satisfy BFSG, BITV 2.0, WCAG, BGG, SGB XI or AnFöVO. The benefit amounts are statutory maximum amounts, not an individual assessment.
- **Outbound requests:** the plugin's PHP makes none. The built-in examples load no external media; the read-aloud uses the browser's own speech synthesis.
- **Personal data:** no visitor form input or raw IP address is stored. The check's selection and toolbar settings stay in the browser view; REST rate limiting stores only a 60-second HMAC-SHA256 digest counter.

---

## 🔒 Ceadúnas agus Cearta Maoine

Distributed under the **GNU General Public License v2 or later**.  
Copyright (C) 2026 Arnold Wender.  
