# Contributing to WM Social Care Accessibility Hub

Thank you for your interest in contributing to **WM Social Care Accessibility Hub**.

---

## Zero Support & Issue Scope

**Zero technical, configuration, or private support is provided.** See [SUPPORT.md](SUPPORT.md):

- Report reproducible code defects **exclusively** via public [GitHub Issues](https://github.com/arnoldwender/wm-wp-social-care-accessibility-hub/issues).
- **Do not** contact the author privately via email, `arnoldwender.com`, or direct messages — private inquiries are never answered.
- Issues and pull requests carry no response or maintenance SLA, and the software is provided strictly **"AS IS"** without warranty or liability.

---

## Core Development Standards

1. **PHP 8.1+ strict typing:** every PHP file starts with `declare(strict_types=1);`, and every file under `includes/` exits before its declaration when loaded outside WordPress.
2. **Self-hosted assets:** no CDNs, Google Fonts or external telemetry loaded by the plugin.
3. **Runs without the hub:** never declare a class against a WM Suite Hub interface that may not exist (`tests/test-standalone-load.php`).
4. **Frontend tokens:** every `var(--…)` used in `includes/Frontend/` is defined in `assets/css/wm-social-care-frontend.css`; text pairs keep at least 4.5:1 contrast, field borders, button boundaries and focus outlines at least 3:1; touch targets at least 44 px.
5. **Nothing announced that is not implemented:** no dead buttons, no fixed success messages, no compliance claims.
6. **Statutory amounts** change only in `includes/Domain/Care_Needs_Checker.php`, with sources and dates.
7. **Translatable strings:** visible text goes through gettext with the literal text domain; regenerate the catalogs with the fleet script after changing strings.

---

## Development Workflow

```bash
# 1. Clone the repository
git clone https://github.com/arnoldwender/wm-wp-social-care-accessibility-hub.git
cd wm-social-care-accessibility-hub

# 2. Run the standalone suites and the mutation check
php tests/test-suite.php
php tests/test-standalone-load.php
bash tests/run-mutations.sh
```

---

## Commit Conventions

- Format: `[Feature] ...`, `[Fix] ...`, `[Security] ...`, `[Docs] ...`.
- A pull request that changes behaviour adds or updates an assertion in `tests/test-social-care-hub.php`, adds the correction to `tests/mutations.php`, and updates README.md, DEVELOPERS.md and CHANGELOG.md where they describe that behaviour.

For support and liability rules, read [SUPPORT.md](SUPPORT.md).
