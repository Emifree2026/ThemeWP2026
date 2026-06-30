# Emifree Theme

Custom WordPress theme for the **Emifree GmbH** air-filtration landing page. Mirrors the live React landing page at `https://github.com/Emifree2026/Landingpage_2026` (commit `e0b55f3e`).

## Structure

```
emifree-theme/
├── style.css                  # theme metadata (required by WordPress)
├── functions.php              # enqueues, AJAX handlers, theme support
├── header.php                 # <head>, <body>, top nav
├── footer.php                 # wp_footer() close
├── front-page.php             # composes the homepage
├── index.php                   # fallback template
├── tailwind.config.js          # Tailwind config (scans ./*.php)
├── postcss.config.js           # PostCSS pipeline
├── package.json                # npm scripts and dev deps
├── assets/
│   ├── css/
│   │   ├── tailwind.src.css    # Tailwind directives (@tailwind base/components/utilities)
│   │   └── main.css            # compiled output (committed for zero-setup)
│   ├── js/
│   │   ├── main.js             # global utilities (sticky header, smooth scroll)
│   │   └── sections/           # per-section JS (one file per section, loaded on demand)
│   ├── images/                 # logos, products, Tech step images, blog hero images
│   └── videos/                 # hero video(s)
├── template-parts/             # modular sections (get_template_part)
├── inc/                        # PHP helpers (SEO, blog, nav, footer data, legal)
└── data/                       # hardcoded content (blog posts)
```

## Build

Tailwind utilities are authored in PHP templates and scanned by Tailwind's content config. To compile the stylesheet:

```bash
npm install
npm run build         # one-shot compile (output: assets/css/main.css)
npm run watch         # recompile on save while developing
```

`assets/css/main.css` is **committed** to the repo so the theme is install-and-go — no build required for end users. Subsequent rebuilds after PHP changes are committed as part of the same change.

## Activation

1. Copy the `emifree-theme/` directory into `wp-content/themes/`.
2. In WP Admin → Appearance → Themes, activate **Emifree Theme**.
3. In WP Admin → Settings → Reading, set "Your homepage displays" to **Your latest posts** (or to a static page assigned the `front-page.php` template).

## Modularity

Sections are PHP files under `template-parts/`. The homepage (`front-page.php`) composes them with `get_template_part()`. To add or remove a section, edit one file.

To author a blog article, drop a PHP file under `data/posts/` returning the post array (see `data/posts/strategic-edge-of-clean-air.php` for the shape).

## Conventions

- **Tailwind only** — no Bootstrap, no other CSS framework.
- **Semantic HTML** — every section uses the appropriate landmark (`<header>`, `<main>`, `<section>`, `<article>`, `<nav>`, `<footer>`).
- **SEO meta + JSON-LD** are injected via `inc/seo.php` helpers; call them at the top of each page template.
- **Per-section JS** lives under `assets/js/sections/` and is enqueued only on pages that need it.

## Compatibility

- WordPress 6.x+
- PHP 7.4+
- Tailwind CSS 3.4 (configured in `tailwind.config.js`)