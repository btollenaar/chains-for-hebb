# Development Plans - PrintStore (Print-on-Demand Storefront)

**Created:** February 13, 2026
**Updated:** February 18, 2026
**Status:** Post-Launch — Continuous Improvement
**Platform:** Laravel 11 + Printful Integration

---

## Current State Summary

PrintStore is a **production-ready print-on-demand storefront** built on Laravel 11 with full Printful integration for automated fulfillment.

**Key Metrics:**
- 330 passing tests (1015+ assertions)
- Full Printful API integration (catalog sync, order fulfillment, webhooks)
- Product variant system with designs and mockups
- Modern 2026 UI (glassmorphism, dark mode, GSAP animations)
- Deployed at bentollenaar.dev

---

## Planned Enhancements

### Priority 1: Printful Catalog Improvements

- **Bulk design upload** — Upload designs to multiple products at once
- **Automated mockup generation** — Trigger Printful mockup API on design upload
- **Catalog sync scheduler** — Auto-refresh Printful catalog cache daily
- **Variant price rules** — Markup rules per product type (e.g., +$5 on hoodies)

### Priority 2: Customer Experience

- **Product filtering by type** — Filter by t-shirts, hoodies, mugs, etc.
- **Size guide component** — Printful size charts embedded in product pages
- **Order tracking page** — Customer-facing tracking with carrier integration
- **Recently viewed products** — localStorage-based browsing history

### Priority 3: Operations

- **Printful cost tracking** — Track base cost vs. selling price per product
- **Profit margin dashboard** — Revenue minus Printful fulfillment costs
- **Bulk product status management** — Archive/activate products in bulk
- **Design library** — Central repository for uploaded design files

### Priority 4: Marketing

- **Collection/lookbook pages** — Curated product groupings (e.g., "Summer 2026")
- **Social sharing** — Product page share buttons with OG meta tags
- **Referral program** — Customer referral codes with discount rewards

---

## Architecture Guidelines

All new features should follow established patterns:

- **Controllers:** Thin controllers, delegate to services
- **Validation:** Use Form Request classes
- **Authorization:** Use Policy classes (never inline checks)
- **Models:** Eager load relationships, use scopes
- **Security:** Sanitize HTML with HTMLPurifier, validate all inputs
- **Testing:** Write feature tests for all new functionality
- **UI (Customer):** Use `btn-gradient`/`btn-glass`, CSS custom properties for dark mode
- **UI (Admin):** Use `btn-admin-primary`/`btn-admin-secondary`, mobile-responsive patterns
- **Database:** Add composite indexes for filtered queries

---

## References

- `CLAUDE.md` — Complete technical guide
- `FUTURE-ENHANCEMENTS.md` — Full feature roadmap
- `TESTING.md` — Testing patterns and best practices
- `README.md` — Quick start and feature overview
- `DATABASE-ERD.md` — Schema visualization
