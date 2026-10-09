# AERO Full-Code Homepage

The current approved homepage preview, rendered inside WordPress's normal theme header/footer. No iframe and no Elementor page content. Activation creates **homepage2 as a draft** and saves the previous front-page settings; it never switches the live homepage automatically or edits page 73.

News is server-rendered from the latest three published WordPress posts in the `news` category, ordered by publication date and then ID descending. Posts without a featured image still appear with an intentional placeholder. Staff update News by publishing normal posts.

## Build and deploy

Run `node wordpress/aero-homepage/build.mjs` (requires `cwebp` on PATH). This prepares only the ten displayed image assets, converts photographs to quality-90 WebP while preserving the supplied ABET JPEG, extracts the main content, inserts the PHP News renderer, and scopes all preview styles to `.aero-home`. Source photography/illustration files remain in `homepage-preview/assets/`.

Zip the plugin directory, excluding this README and build tooling if desired. Upload through WordPress Plugins → Add Plugin → Upload Plugin, then activate. Preview the new draft from Pages; publish after responsive review. In Settings → Reading, select **homepage2** as the static homepage, leaving Posts page and other settings unchanged.

The page uses the active theme's `get_header()` and `get_footer()`, retaining existing Elementor header 57 and footer 63. Styling loads only on the registered homepage2 page.

When homepage2 is the front page, the existing Home menu items are rendered with the root URL (rather than WordPress's newly assigned `/home/` permalink for page 73). Labels, order, menus, and template data are not edited. This rendering filter stops applying if the front-page assignment is rolled back.

## Rollback

Set Settings → Reading → Homepage back to **Home (page 73)**. The old layout is not modified. The initial reading settings are also retained in the `aero_home_previous_front_settings` WordPress option. Keep this plugin active while homepage2 is used; deactivating it removes the code template, not the page or old homepage.
