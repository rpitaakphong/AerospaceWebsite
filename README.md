# Aerospace Engineering website work

Source and local previews for the Aerospace Engineering Program at Chulalongkorn University.

- `homepage-preview/` — responsive homepage prototype. Serve this folder over HTTP to preview it locally. Its News section reads the live WordPress News feed and uses saved cards when the feed is unavailable.
- `capstone-modern/` — Capstone Project showcase development version, with project data and design assets.
- `capstone-data/` — source project records and imagery used for the showcase.
- `wordpress/aero-capstone-showcase/` — installable WordPress plugin source for the Capstone showcase; `wordpress/aero-capstone-showcase.zip` is a packaged build.
- `wordpress/aero-homepage/` — deployed full-code homepage2 plugin, built from the approved preview with server-rendered WordPress News and the existing theme header/footer. The versioned ZIP alongside it is the installable package; see its deployment record for validation and rollback.
- `wordpress-plugins/` — additional WordPress plugin source and packaged builds.

Pushing this repository to GitHub does not deploy changes to the live WordPress site. Homepage2 is now published through the full-code plugin, not Elementor. Plugin updates must be built and installed separately.
