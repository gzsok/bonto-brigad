# AGENTS.md

## Project overview

Bontó Brigád is a Hungarian, single-page website for a local demolition,
renovation, and maintenance crew serving Pécs and the surrounding area.

The project is intentionally lightweight:

- static HTML;
- component-oriented CSS;
- a small amount of inline vanilla JavaScript;
- no framework, package manager, dependency installation, or build step.

The website must remain directly usable by opening `index.html` or serving the
repository as a static directory.

## Project structure

- `index.html`: page content, metadata, navigation, and browser interactions
- `components/base.css`: design tokens, resets, typography, and shared utilities
- `components/header.css`: header, brand, navigation, and mobile menu
- `components/hero.css`: hero section and primary calls to action
- `components/sections.css`: promise, services, about, references, contact, and footer
- `components/responsive.css`: reveal behavior, breakpoints, mobile layouts, and reduced-motion rules
- `assets/logo-mark.svg`: standalone Bontó Brigád brand mark and favicon
- `assets/logo-lockup.svg`: horizontal mark and wordmark combination
- `assets/og.png`: social-sharing preview image
- `sitemap.xml`: search-engine sitemap for `https://bontobrigad.hu/`
- `robots.txt`: crawler rules and sitemap reference
- `.htaccess`: Rackhost/Apache redirects, compression, caching, and security headers

Keep new project-owned visual assets under `assets/`. Keep page-level component
styles under `components/`.

## Working conventions

- Preserve the dependency-free static architecture unless the user explicitly
  requests a different setup.
- Keep the website one page unless the user explicitly asks for additional
  pages.
- Preserve Hungarian user-facing copy, accents, metadata, telephone numbers,
  email addresses, and UTF-8 encoding.
- Keep content and document structure in `index.html`; do not generate HTML
  fragments dynamically for static content.
- Do not move CSS back into inline `<style>` blocks.
- Put global primitives and design tokens in `components/base.css`.
- Put component-specific rules in the closest matching component stylesheet.
- Put breakpoint overrides and reduced-motion behavior in
  `components/responsive.css`.
- Reuse existing classes and design tokens before introducing new ones.
- Keep browser code framework-free and compatible with modern vanilla
  JavaScript.
- Avoid introducing a build step solely for formatting, asset loading, or small
  interactions.

## Brand and visual system

The visual identity is industrial, direct, and modern rather than generic
construction-themed.

- Primary ink: `#111714`
- Signal green: `#c9ff35`
- Warm paper: `#f4f1e9`
- Display typeface: Barlow Condensed
- Body typeface: Manrope

Preserve the strong condensed typography, architectural grid motifs, restrained
blueprint language, and high-contrast calls to action.

Use `assets/logo-mark.svg` for compact brand placements and
`assets/logo-lockup.svg` when a horizontal wordmark asset is needed. Do not
redraw, distort, recolor, or replace these assets without an explicit branding
request.

Do not introduce stock construction imagery, orange safety-color clichés,
glossy effects, or unrelated icon styles. Real reference photographs may be
added when supplied or approved by the user.

## Content constraints

- Treat the existing business information as authoritative.
- Do not invent qualifications, guarantees, prices, project counts,
  testimonials, service areas, or availability claims.
- Keep the reference section honest while project photography is unavailable.
- Telephone links must use `tel:+36302299269`.
- Email links must use `mailto:info@bontobrigad.hu`.
- The stated service area is Pécs and its surroundings.

## Accessibility and interaction

- Preserve semantic landmarks, heading order, and the skip link.
- All interactive controls must remain keyboard accessible.
- Keep visible `:focus-visible` styles.
- The mobile navigation must update `aria-expanded`, close after selecting a
  link, close on Escape, and prevent background scrolling while open.
- Decorative imagery must use empty alternative text or be hidden from
  assistive technologies.
- Preserve the `prefers-reduced-motion` behavior.
- Maintain sufficient text and control contrast.
- Do not rely on hover alone to communicate essential information.

## Responsive behavior

Check changes at desktop, tablet, and narrow mobile widths. In particular,
verify:

- header and mobile navigation;
- hero headline wrapping and primary actions;
- service-card stacking;
- about-section columns and trait cards;
- reference-card layout;
- contact details without horizontal overflow;
- footer stacking.

Avoid fixed widths that can create horizontal scrolling on small screens.

## Metadata and assets

- Preserve the page title, description, Open Graph metadata, Twitter card
  metadata, theme color, and favicon unless the task explicitly changes them.
- Treat `https://bontobrigad.hu/` as the canonical production URL. Redirect
  HTTP and `www` requests to this HTTPS, non-`www` address.
- Keep `assets/og.png` at a social-card-friendly landscape ratio.
- Use relative paths so the site continues to work from static hosting and
  local preview.
- Optimize new raster assets before committing them.

## Validation

There is no automated test suite. After relevant changes:

1. Confirm every referenced local asset and stylesheet exists.
2. Confirm `index.html` parses without structural errors.
3. Confirm CSS braces are balanced and the stylesheet loading order remains:
   `base`, `header`, `hero`, `sections`, `responsive`.
4. Check navigation anchors, phone links, email links, and mobile-menu behavior.
5. Check desktop and mobile layouts when visual behavior changed.
6. Review `git diff` and ensure no unrelated or generated files are included.

Do not add temporary build output, local server files, hosting configuration, or
generated dependency folders to the repository.

## Publishing and external actions

Keep work local by default.

- Do not deploy, host, publish, push, create a pull request, or configure a
  remote service unless the user explicitly requests that exact action.
- Do not create `.openai/hosting.json`, deployment bundles, or hosting projects
  as part of ordinary design or development work.
- A request to edit, redesign, validate, or preview the website does not imply
  permission to publish it.
