# Frontend Redesign Plan — Chains for Hebb

**Created:** 2026-02-24
**Status:** Not started
**Audited via:** Live browser automation (Firecrawl) + source code review

---

## Audit Summary

The site was audited by browsing all major pages on `https://chains.bentollenaar.dev` and reading the source templates, CSS, and Tailwind config.

### P0 — CRITICAL: Header Nav Invisible on All Non-Hero Pages

The header starts transparent with white text (`nav-link--transparent: color: rgba(255,255,255,0.9)`). This is designed for the dark hero overlay on the homepage. On **every other page**, there's no dark background behind it — nav links are white on parchment (#F5F1E8), essentially invisible until you scroll past 50px.

**Affected pages:** Login, Register, Shop, Blog, Gallery, Progress, Cart, Checkout, Orders, Profile, Wishlist, Search, Legal pages, all auth pages — everything except Home, Donate, and Events (which have dark hero banners).

**Root cause:** `header.blade.php` Alpine state:
```js
x-data="{ scrolled: false }"
x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 50 })"
:class="scrolled ? 'glass shadow-glass' : 'bg-transparent'"
```
Nav links use `:class="scrolled ? 'nav-link--scrolled' : 'nav-link--transparent'"` — the transparent variant is white text, designed for dark hero backgrounds only.

### P1 — Pages Without Hero Banners Look Flat

Shop, Blog, Gallery, and Progress pages jump straight into content with no visual anchor. Compare to Donate and Events which have dark-gradient hero banners — the inconsistency is jarring.

### P2 — Auth Pages Are Generic

Login and Register are bare forms on a plain parchment background. For a community fundraiser with this much personality in the hero and homepage sections, the auth pages feel like they belong to a different site.

### P3 — Minor Issues

- `card-glass` has `:hover` lift/scale animation — awkward on form containers (login/register cards shouldn't bounce)
- Meta descriptions still say "appointments" (from the PrintStore fork)
- Footer shows empty `tel:` link with no phone number
- Register page subtitle says "Join us to book appointments and manage orders"

---

## Phase 1: Fix the Header (P0)

**Priority:** Do first — every non-hero page is broken.

### Strategy

Use a server-side `@section('pageHero')` flag so the header knows its initial state before Alpine runs (no flash of wrong colors). In `<x-app-layout>` component layouts, the child view is evaluated first, so `@hasSection` checks in the layout work correctly.

### Files to Change

**1. `resources/views/layouts/app.blade.php`**

Add `data-page-hero` attribute to `<body>` when a page declares a hero:

```diff
- <body class="font-sans antialiased transition-colors duration-300" style="background-color: var(--surface); color: var(--on-surface);">
+ <body class="font-sans antialiased transition-colors duration-300" @hasSection('pageHero') data-page-hero @endif style="background-color: var(--surface); color: var(--on-surface);">
```

**2. `resources/views/components/header.blade.php`**

Update Alpine to check for the `data-page-hero` attribute:

```diff
  <header
-     x-data="{ scrolled: false }"
-     x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 50 })"
+     x-data="{ scrolled: !document.body.hasAttribute('data-page-hero') }"
+     x-init="
+         const hasHero = document.body.hasAttribute('data-page-hero');
+         window.addEventListener('scroll', () => { scrolled = hasHero ? window.scrollY > 50 : true });
+     "
      class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
      :class="scrolled ? 'glass shadow-glass' : 'bg-transparent'"
  >
```

**3. Pages with dark hero banners — add the section flag:**

Add `@section('pageHero', true)` right after the title section in each of these files:

- `resources/views/home.blade.php` (after line 2)
- `resources/views/donate/index.blade.php` (after line 2)
- `resources/views/events/index.blade.php` (after line 2)

Example:
```blade
<x-app-layout>
    @section('title', 'Help Build a Disc Golf Course at Hebb Park')
    @section('pageHero', true)
    ...
```

### Verification

After implementing, every page should have:
- **Hero pages (home, donate, events):** Transparent header with white text at top, switching to glass+dark-text on scroll
- **All other pages:** Glass header with dark text immediately (no scroll needed)

---

## Phase 2: Add Hero Banners to Flat Pages (P1)

**Priority:** High — gives visual hierarchy and fixes the flat feel.

### Pages Needing Heroes

| Page | File | Hero Heading | Hero Subtext |
|------|------|-------------|-------------|
| Shop | `resources/views/products/index.blade.php` | "Shop Merch" | "Every purchase helps fund disc golf at Hebb Park. 100% of profits go to the cause." |
| Blog | `resources/views/blog/index.blade.php` | "Updates & Stories" | "Follow along as we build disc golf at Hebb Park." |
| Gallery | `resources/views/gallery/index.blade.php` | "From the Park" | "Photos from Hebb Park, work parties, and community events." |
| Progress | `resources/views/progress/index.blade.php` | "Building Progress" | "Track every milestone as we bring disc golf to Hebb Park." |

### Template Pattern

Use the same hero pattern as donate/events. Each page should:

1. Add `@section('pageHero', true)` so the header fix from Phase 1 works
2. Wrap existing content below the hero (remove redundant page headers)

```blade
@section('pageHero', true)

{{-- Hero Banner --}}
<section class="relative py-24 px-4 overflow-hidden"
         style="margin-top: -72px; padding-top: calc(72px + 4rem);
                background: linear-gradient(135deg, #2D5016, #1A1A2E);">
    @if(file_exists(public_path('images/generated/PAGE-hero.webp')))
    <div class="absolute inset-0"
         style="background-image: url('{{ asset('images/generated/PAGE-hero.webp') }}');
                background-size: cover; background-position: center; opacity: 0.25;"></div>
    @endif
    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-[var(--surface)]"></div>
    <div class="relative z-10 max-w-4xl mx-auto text-center">
        <h1 class="font-display text-white text-fluid-4xl font-bold uppercase tracking-tight mb-4">
            HEADING HERE
        </h1>
        <p class="text-white/80 text-fluid-base max-w-2xl mx-auto">
            SUBTEXT HERE
        </p>
    </div>
</section>
```

### Per-Page Notes

**Shop (`products/index.blade.php`):**
- Remove the existing "Catalog" / "Our Products" header block (lines 8-12)
- Move the search bar below the hero, keep everything else

**Blog (`blog/index.blade.php`):**
- Replace whatever static header exists with the hero banner

**Gallery (`gallery/index.blade.php`):**
- Good candidate for a background image in the hero if one exists

**Progress (`progress/index.blade.php`):**
- Could include the inline progress bar in the hero (like donate page does)

---

## Phase 3: Redesign Auth Pages (P2)

**Priority:** Medium — lower traffic but completes the polish.

### Files

- `resources/views/auth/login.blade.php`
- `resources/views/auth/register.blade.php`
- `resources/views/auth/forgot-password.blade.php`
- `resources/views/auth/reset-password.blade.php`
- `resources/views/auth/verify-email.blade.php`
- `resources/views/auth/confirm-password.blade.php`

### Design Direction

**Login & Register — split-screen layout:**

```
┌──────────────────────────────────────────────┐
│  [Dark gradient + park image]  │  [Form]     │
│  Chains for Hebb branding      │  Email      │
│  "Join X supporters"           │  Password   │
│  Social proof / progress bar   │  [Log In]   │
│                                │             │
└──────────────────────────────────────────────┘
```

- Left side: dark gradient (`#2D5016` → `#1A1A2E`) with nature/park imagery, fundraising social proof
- Right side: form on parchment background
- On mobile: stack vertically — compact hero banner above the form
- Add `@section('pageHero', true)` since the left panel serves as visual anchor

**Simpler auth pages (forgot-password, verify-email, confirm-password):**
- Keep centered card layout
- The Phase 1 fix already handles the header (these pages don't have heroes → glass header with dark text)
- Optionally add a subtle hero strip or a dark-gradient background to the top portion

### Content Updates

- Login meta: "Log in to your Chains for Hebb account"
- Register meta: "Create an account to support Chains for Hebb"
- Register subtitle: "Join the community building disc golf at Hebb Park"
- Remove ALL "appointments" references

---

## Phase 4: Polish Fixes (P3)

**Priority:** Low — quick wins, can be done alongside any phase.

### 4a. Remove hover animation from form cards

**File:** `resources/css/design-system.css`

Add a static variant after the existing `.card-glass:hover` rule (~line 339):

```css
.card-glass--static,
.card-glass--static:hover {
    transform: none !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06), 0 1px 2px rgba(0, 0, 0, 0.04) !important;
}
```

Then apply `card-glass--static` to auth form cards in `login.blade.php`, `register.blade.php`, etc.:
```diff
- <div class="card-glass rounded-2xl p-8" data-animate="fade-up">
+ <div class="card-glass card-glass--static rounded-2xl p-8" data-animate="fade-up">
```

### 4b. Fix meta descriptions

| File | Old | New |
|------|-----|-----|
| `auth/login.blade.php` | "Access your account to manage appointments and orders." | "Log in to your Chains for Hebb account." |
| `auth/register.blade.php` | "Create your account to book appointments and manage orders." | "Create an account to support Chains for Hebb." |

### 4c. Fix register subtitle

**File:** `resources/views/auth/register.blade.php` (line 10)

```diff
- <p class="text-sm mt-2" style="color: var(--on-surface-muted);">Join us to book appointments and manage orders</p>
+ <p class="text-sm mt-2" style="color: var(--on-surface-muted);">Join the community building disc golf at Hebb Park</p>
```

### 4d. Fix empty phone link in footer

**File:** `resources/views/components/footer.blade.php` (lines 48-53)

Wrap the phone `<li>` in a conditional:

```diff
+ @if(!empty($contactSettings['phone']))
  <li>
      <a href="tel:{{ $contactSettings['phone'] }}" ...>
          ...
      </a>
  </li>
+ @endif
```

---

## Execution Order

1. **Phase 1** — Fix header nav (P0, ~15 min, 5 files)
2. **Phase 4** — Quick polish fixes (P3, ~10 min, can parallelize)
3. **Phase 2** — Add hero banners to flat pages (~30 min, 4 files)
4. **Phase 3** — Auth page redesign (~45 min, 6 files)

### Testing Checklist

After all phases, verify on the live site:

- [ ] Homepage: transparent header → glass on scroll
- [ ] Login/Register: readable nav immediately (no white-on-white)
- [ ] Shop: hero banner + readable nav
- [ ] Blog: hero banner + readable nav
- [ ] Gallery: hero banner + readable nav
- [ ] Progress: hero banner + readable nav
- [ ] Donate: transparent header (has dark hero) → glass on scroll
- [ ] Events: transparent header (has dark hero) → glass on scroll
- [ ] Cart: readable nav immediately
- [ ] Dark mode: verify all pages work in both themes
- [ ] Mobile: hamburger menu works, hero banners responsive
- [ ] Footer: no empty phone link
- [ ] Auth meta descriptions: no "appointments" text
