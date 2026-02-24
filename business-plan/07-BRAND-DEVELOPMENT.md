# 07 - Brand Development

**Last Updated:** February 12, 2026

---

## Brand Positioning

### Core Position

**For** environmentally conscious homeowners and young professionals
**Who** want to reduce their household's environmental impact without sacrificing quality or aesthetics
**Our brand** is a curated marketplace of premium eco-friendly home products and specialty accessories
**That** makes sustainable living beautiful, accessible, and affordable
**Unlike** mass-market retailers with token "green" sections or premium-priced eco boutiques
**We** offer a carefully curated selection of genuinely sustainable products at fair prices, backed by transparent sourcing and a community of like-minded people.

### Brand Values

1. **Authenticity** - Every product is genuinely sustainable, not greenwashed
2. **Curation over catalog** - We choose fewer, better products
3. **Accessibility** - Sustainable living shouldn't require a premium income
4. **Transparency** - We share the "why" behind every product choice
5. **Community** - Building a movement, not just a store

### Target Customer Personas

**Primary: "Conscious Carly" (60% of audience)**
- Age: 25-40
- Income: $50-90K
- Values: Sustainability, health, aesthetics
- Shopping behavior: Researches before buying, values brand story
- Pain point: Overwhelmed by greenwashing, wants trusted curation
- Channels: Instagram, Pinterest, email newsletters

**Secondary: "Practical Pete" (25% of audience)**
- Age: 30-50
- Income: $60-120K
- Values: Quality, durability, cost-effectiveness
- Shopping behavior: Pragmatic, wants long-lasting products
- Pain point: Tired of replacing cheap items, sees eco as quality
- Channels: Google search, YouTube reviews, email

**Tertiary: "Gift-Giving Grace" (15% of audience)**
- Age: 28-55
- Income: Varies
- Values: Thoughtful gifting, unique finds
- Shopping behavior: Seasonal (holidays, birthdays, housewarmings)
- Pain point: Wants meaningful, not generic, gifts
- Channels: Pinterest, Instagram, Google Shopping

---

## Brand Naming

### Naming Criteria

1. **Memorable** - Easy to spell, pronounce, and remember
2. **Available** - .com domain + social handles available
3. **Evocative** - Suggests sustainability, home, nature without being generic
4. **Scalable** - Works beyond just eco products if you expand
5. **Unique** - Not easily confused with existing brands
6. **Short** - Ideally 2-3 syllables, max 12 characters

### Naming Directions

**Direction 1: Nature + Home**
Combines natural elements with domestic warmth.
- Mossvale
- Fernhome
- Rootwell
- Hearthleaf
- Willowroot
- Greenvale
- Oakstead

**Direction 2: Earth + Action**
Suggests positive environmental impact.
- GreenShift
- EcoLoop
- TerraSwap
- PlanetKind
- EarthStep
- Sustaina

**Direction 3: Minimal + Modern**
Clean, contemporary feel (think Everlane, Allbirds).
- Verd (French for green)
- Nolla (Finnish for zero)
- Klar (German for clear/pure)
- Renu
- Aera
- Viva Home

**Direction 4: Compound / Invented**
Distinctive, ownable, no existing associations.
- Greenly
- Ecoliv
- Sustain & Co
- Verdant Home
- The Good Swap
- Purely Home

### Name Evaluation Matrix

| Name | Memorable | Domain Likely | Evocative | Scalable | Unique | Score |
|------|-----------|--------------|-----------|----------|--------|-------|
| Mossvale | 8 | 7 | 8 | 7 | 8 | 38 |
| Hearthleaf | 7 | 8 | 9 | 6 | 9 | 39 |
| GreenShift | 8 | 5 | 8 | 8 | 6 | 35 |
| Verd | 9 | 6 | 7 | 8 | 7 | 37 |
| Greenly | 9 | 5 | 8 | 8 | 6 | 36 |
| The Good Swap | 7 | 6 | 9 | 6 | 7 | 35 |

### Verification Steps (Before Final Decision)

1. **Domain search:** Check .com availability (Namecheap, GoDaddy)
2. **Social handles:** Check @name on Instagram, Pinterest, TikTok, Twitter
3. **Trademark search:** USPTO TESS database (free, tess2.uspto.gov)
4. **Google search:** Ensure no major brand conflicts
5. **Say it out loud:** Does it sound good in conversation? "I got this from ___"
6. **Ask 5 people:** Show the name, ask what they think it sells

---

## Visual Identity

### Design Direction

The existing Laravel app uses a modern 2026 design system with glassmorphism, gradients, and dark mode. The new brand should leverage this system while establishing its own visual personality.

**Mood:** Clean, natural, premium but accessible, not "granola" or "hippie"

**Visual References:**
- Everlane (clean minimalism)
- Patagonia (rugged authenticity)
- Public Goods (modern simplicity)
- Reformation (eco + style)

### Color Palette Recommendation

**Primary Palette:**
```
Forest Green: #2D5016 (primary brand color - trust, nature, eco)
Warm Cream: #F5F0E8 (background - warmth, natural feel)
Charcoal: #1A1A2E (text - modern, readable)
```

**Accent Palette:**
```
Sage: #87A878 (secondary green - softer, calming)
Terracotta: #C67B5C (warm accent - earthy, inviting)
Sky Blue: #6CB4EE (highlight - freshness, water)
```

**Implementation:**
These colors can be mapped to the existing design system CSS custom properties:
```css
:root {
    --surface: #F5F0E8;           /* Warm cream background */
    --surface-raised: #FFFFFF;     /* White cards */
    --on-surface: #1A1A2E;        /* Charcoal text */
    --on-surface-muted: #6B7280;  /* Muted text */
    --accent-primary: #2D5016;    /* Forest green */
    --accent-secondary: #87A878;  /* Sage */
    --accent-warm: #C67B5C;       /* Terracotta */
}
```

The app's existing `vivid-*` colors would be replaced with this natural palette, and `btn-gradient` would use a green-to-sage gradient instead of purple-to-blue.

### Typography Recommendations

**Keep from existing system:**
- **Inter** for body text (already loaded, excellent readability)
- **Space Grotesk** for display headings (modern, clean)

**Alternative if wanting more organic feel:**
- **DM Serif Display** for headings (elegant, warmer)
- **Inter** for body (keep for readability)

### Logo Direction

**Style:** Wordmark + minimal icon

**Icon Concepts:**
- Abstract leaf/circle combination
- Minimalist house with leaf
- Geometric plant/growth symbol
- Simple circular seal/badge

**Logo Requirements:**
- Works at small sizes (favicon, social avatar)
- Looks good in both light and dark mode
- Mono-color version available (for packaging, invoices)
- SVG format for scalability

**Creation Options:**
| Option | Cost | Quality | Timeline |
|--------|------|---------|----------|
| Looka/Brandmark AI | $20-65 | Good for starting | Instant |
| 99designs | $299-999 | Professional | 5-7 days |
| Fiverr designer | $50-200 | Variable | 3-7 days |
| Local designer | $500-2,000 | Best | 2-4 weeks |

**Recommendation:** Start with Looka ($65 for brand kit) to get initial assets quickly. Invest in professional redesign when revenue hits $3,000/month.

---

## Brand Voice & Messaging

### Voice Characteristics

| Attribute | What It Sounds Like | What It Doesn't Sound Like |
|-----------|--------------------|-----------------------------|
| Warm | "We're glad you're here" | "Welcome, valued customer" |
| Knowledgeable | "Here's why bamboo is better" | "Studies suggest potential benefits" |
| Encouraging | "Every swap counts" | "You need to do more" |
| Honest | "This won't save the world alone, but..." | "This single product will save the planet" |
| Playful | "Your kitchen is about to get an upgrade" | "Purchase our premium kitchenware" |

### Key Messages

**Tagline Options:**
- "Better choices, beautiful homes."
- "Sustainable living, simplified."
- "Live lighter."
- "The greener way home."
- "Make the swap."

**Elevator Pitch:**
> "[Brand Name] curates premium eco-friendly home products that make sustainable living feel effortless. From bamboo kitchen essentials to specialty coffee accessories, we help you reduce waste without sacrificing quality or style.""

**Product Descriptions Tone:**
```
DON'T:
"This eco-friendly bamboo toothbrush is made from sustainable materials
and is better for the environment. Buy now to reduce your carbon footprint."

DO:
"Tired of adding another plastic toothbrush to the landfill every 3 months?
This bamboo beauty does the same job and breaks down naturally when you're done.
Soft bristles, sleek design, zero guilt."
```

**Email Tone:**
```
DON'T: "Dear Customer, we are pleased to announce our new product line."
DO: "Hey [name], we just added something you're going to love."
```

### Content Themes

| Theme | Purpose | Example Content |
|-------|---------|-----------------|
| "The Swap" | Show eco alternatives | "Swap #14: Ditch plastic wrap for beeswax wraps" |
| "Behind the Product" | Transparency/sourcing | "Why we chose this bamboo: sourced from..." |
| "Impact Numbers" | Quantify the difference | "One reusable bag replaces 700 plastic bags/year" |
| "Real Homes" | Customer stories/UGC | "How Sarah transformed her zero-waste kitchen" |
| "Quick Wins" | Actionable tips | "5 swaps you can make this weekend" |

---

## Social Media Presence

### Handle Availability Check Process

For your chosen brand name, check availability on:
- [ ] Instagram (@brandname)
- [ ] Pinterest (/brandname)
- [ ] TikTok (@brandname)
- [ ] Twitter/X (@brandname)
- [ ] Facebook (/brandname)
- [ ] YouTube (@brandname)

**If handles taken:** Try variations:
- @shopbrandname
- @brandname.co
- @getbrandname
- @brandname_home

### Profile Setup

**Bio Template:**
```
[Brand Name] | Curated eco-friendly home products
Sustainable living made beautiful
Free shipping on orders $45+
Shop: [link]
```

**Link in Bio:** Use Linktree (free) or build a custom landing page with the Laravel app.

### Visual Consistency

- Use same color palette across all platforms
- Consistent profile picture (logo icon)
- Cover photos with brand colors and tagline
- Pinterest board covers in brand style
- Instagram highlight covers in brand colors

---

## Packaging & Physical Branding

### Phase 1: Minimal Branded Touch

| Item | Specs | Estimated Cost |
|------|-------|---------------|
| Logo sticker (circle, 2") | Kraft paper, one-color | $0.05-0.10/unit (bulk 500+) |
| Thank-you card (4x6") | Kraft cardstock, two-sided | $0.15-0.20/unit (bulk 500+) |
| | Front: "Thank you for choosing a greener path" + logo | |
| | Back: 10% off next order code + social handles | |

### Phase 2: Branded Experience

| Item | Specs | Estimated Cost |
|------|-------|---------------|
| Custom mailer (kraft) | Branded with logo + tagline | $0.50-1.00/unit |
| Tissue paper | Logo pattern on recycled paper | $0.15-0.25/unit |
| Product care card | Specific to product category | $0.10-0.15/unit |
| Sticker pack | 2-3 brand stickers | $0.10-0.20/unit |

### Phase 3: Premium Unboxing

| Item | Specs | Estimated Cost |
|------|-------|---------------|
| Custom box | Branded recycled cardboard | $1.00-2.00/unit |
| Custom tape | Logo + pattern | $0.10-0.15/unit |
| Branded tissue | Full custom pattern | $0.20-0.30/unit |
| Premium thank-you card | Thick cardstock, premium feel | $0.20-0.30/unit |

**Packaging Supplier:** EcoEnclose (ecoenclose.com) - specialized in sustainable packaging.

---

## Brand Launch Assets Checklist

### Must-Have Before Launch
- [ ] Brand name finalized
- [ ] Domain registered
- [ ] Logo (primary + icon + mono versions)
- [ ] Color palette defined
- [ ] Social media accounts created (Instagram, Pinterest, TikTok)
- [ ] Thank-you cards printed (initial 200-500)
- [ ] Logo stickers printed (initial 500)
- [ ] Brand voice guide written (1-pager)
- [ ] Email templates branded
- [ ] Website configured with brand colors/logo

### Nice-to-Have (First 3 Months)
- [ ] Professional product photography (branded backgrounds)
- [ ] Brand video (30-second intro for social)
- [ ] Pinterest board covers
- [ ] Instagram highlight covers
- [ ] Custom packaging materials
- [ ] Branded invoice/packing slip template

### Estimated Brand Setup Cost

| Item | Cost | Notes |
|------|------|-------|
| Domain (.com) | $12-15 | Annual |
| Logo (AI-generated) | $20-65 | Looka or Brandmark |
| Stickers (500 units) | $30-50 | StickerMule or similar |
| Thank-you cards (500 units) | $40-75 | Printful or Vistaprint |
| Social media setup | $0 | Free accounts |
| Canva Pro (annual) | $120 | For ongoing graphics |
| **Total** | **$222-325** | |

---

*Brand decisions should be made early but can evolve. Don't let perfection prevent launch - start with a solid foundation and refine based on customer feedback.*
