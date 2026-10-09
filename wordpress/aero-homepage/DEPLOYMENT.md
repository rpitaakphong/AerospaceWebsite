# Homepage2 deployment — 10 October 2026

- Site: https://aero.ise.eng.chula.ac.th/
- Full-code page: **homepage2, ID 1536**, published and selected as the static front page.
- Previous front page: **Home, ID 73**, retained unchanged; Posts page remains unassigned (`0`).
- Header/footer: existing Elementor templates **57 / 63**, verified in the rendered DOM.
- Package: `wordpress/aero-homepage-1.0.2.zip`. Photos optimized as WebP to fit the server's upload limit. Original sources remain in the homepage preview.
- News verified against published dashboard posts: **1479** (26 April 2026), **1407** (8 April 2026, 15:45), **1404** (8 April 2026, 15:41), all in News.
- Draft reviewed at **1512×863**, **768×1024**, and **390×844**: no horizontal overflow; imagery loads, full ABET logo visible, keyboard focus has a 3px red outline. All twelve unique content destinations opened successfully. No captured homepage console errors.
- Scoped asset versioning invalidates prior stylesheet/script URLs. No active page-cache plugin was present in the installed plugin list. Root URL verified after the Reading setting was saved.

## Rollback

Settings → Reading → Homepage: **Home** (page 73) → Save Changes. Do not change Posts page. The plugin also retains the initial setting snapshot in `aero_home_previous_front_settings`.

News continues to update through normal WordPress post publishing. Curated layout/content edits belong in the preview source followed by the plugin build and ZIP update, not Elementor.
