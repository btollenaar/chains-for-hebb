# 02 - Fulfillment Strategy

**Last Updated:** February 12, 2026

---

## Fulfillment Model Evolution

The fulfillment strategy evolves across three phases to balance risk, cost, and customer experience:

| Phase | Model | When | Monthly Orders | Investment |
|-------|-------|------|----------------|------------|
| 1 | Dropshipping (CJ + Printful) | Months 1-4 | 0-100 | $0 upfront |
| 2 | Hybrid (Dropship + Direct Sourcing) | Months 5-8 | 100-300 | $500-1,500 inventory |
| 3 | 3PL Fulfillment | Months 9+ | 300+ | 3PL setup + inventory |

---

## Phase 1: Dropshipping (Months 1-4)

### CJDropshipping (Primary Fulfillment)

**Best For:** Testing products with zero inventory risk.

**Fee Structure:**
| Fee Type | Amount | Notes |
|----------|--------|-------|
| Platform fee | $0/month | Free to use |
| Product cost | Varies | Pay wholesale per order |
| Shipping (US warehouse) | $3-7 per order | 7-15 day delivery |
| Shipping (China warehouse) | $5-12 per order | 15-25 day delivery |
| US warehousing storage | Free for 90 days | Then per-cubic-foot fees |
| Custom packaging | $0.50-2.00 per order | Optional branded inserts |

**Shipping Speed Options:**
| Method | US Delivery | Cost (1 lb) | Best For |
|--------|------------|-------------|----------|
| CJ Packet (China) | 15-25 days | $5-8 | Testing products |
| ePacket (China) | 12-20 days | $6-10 | Budget option |
| YunExpress (China) | 8-15 days | $7-12 | Mid-tier |
| US Warehouse | 3-7 days | $3-5 | Proven products |
| USPS (US Warehouse) | 2-5 days | $4-7 | Premium option |

**Recommended Strategy:**
1. Start with China shipping for product testing (cheaper, slower)
2. Move winning products to US warehouse (faster shipping, better experience)
3. Keep 30-day supply in CJ US warehouse for top sellers

**CJ Process Flow:**
```
Customer Order → Your Laravel App → CJ API → CJ Sources & Ships → Customer Receives
                                    ↓
                              Tracking # sent back → Email to customer
```

### Printful (Print-on-Demand + Branded Items)

**Best For:** Custom-branded eco products (tote bags, branded wraps, apparel).

**Fee Structure:**
| Fee Type | Amount | Notes |
|----------|--------|-------|
| Platform fee (Free plan) | $0/month | Full API access |
| Platform fee (Growth plan) | $24.99/month | Up to 33% off products, free at $12K/yr |
| T-shirt (standard) | ~$10-13 wholesale | Retail $24-32 |
| Tote bag (organic cotton) | ~$8-12 wholesale | Retail $22-28 |
| Mug (ceramic) | ~$6-8 wholesale | Retail $18-24 |
| US Shipping (first item) | ~$4.69 | Additional items ~$1.25 |
| Branded inside label | ~$2.49 one-time | Per design |

**Printful Process Flow:**
```
Customer Order → Your Laravel App → Printful API → Printful Prints & Ships → Customer
                                     ↓
                              Tracking # webhook → Email to customer
```

**Printful Product Opportunities:**
- Branded reusable tote bags ("Your sustainable choice")
- Custom-printed beeswax wrap instructions/care cards
- Branded packaging inserts (sustainability mission cards)
- Eco-lifestyle apparel (t-shirts, hoodies with eco messaging)

### Phase 1 Cost Per Order Example

**Example: Bamboo Toothbrush 4-Pack**
| Component | Cost |
|-----------|------|
| Product (CJ wholesale) | $3.50 |
| CJ US Warehouse shipping | $4.00 |
| Stripe fee (2.9% + $0.30 on $12 sale) | $0.65 |
| Packaging insert | $0.50 |
| **Total COGS** | **$8.65** |
| **Retail price** | **$12.00** |
| **Gross profit** | **$3.35 (28%)** |

**Example: Beeswax Wraps 3-Pack**
| Component | Cost |
|-----------|------|
| Product (CJ wholesale) | $5.00 |
| CJ US Warehouse shipping | $4.00 |
| Stripe fee (2.9% + $0.30 on $18 sale) | $0.82 |
| Packaging insert | $0.50 |
| **Total COGS** | **$10.32** |
| **Retail price** | **$18.00** |
| **Gross profit** | **$7.68 (43%)** |

**Example: Gooseneck Electric Kettle**
| Component | Cost |
|-----------|------|
| Product (CJ wholesale) | $22.00 |
| CJ US Warehouse shipping | $6.00 |
| Stripe fee (2.9% + $0.30 on $65 sale) | $2.19 |
| Packaging insert | $0.50 |
| **Total COGS** | **$30.69** |
| **Retail price** | **$65.00** |
| **Gross profit** | **$34.31 (53%)** |

---

## Phase 2: Hybrid Model (Months 5-8)

### Direct Alibaba Sourcing for Winners

Once a product sells 20+ units/month consistently, source directly from manufacturers.

**Benefits:**
- 30-50% lower unit costs vs CJDropshipping
- Custom branding/packaging options
- Higher quality control with factory inspections
- Exclusive product variations

**Process:**
1. Identify top 5-10 selling products
2. Search Alibaba for manufacturers (not trading companies)
3. Request samples from 3-5 factories per product
4. Compare quality, pricing, MOQ, lead times
5. Place initial order (typically 100-500 units)
6. Ship to CJ US warehouse or directly to 3PL

**Example Savings:**

| Product | CJ Price | Alibaba Direct | Savings |
|---------|----------|---------------|---------|
| Beeswax wraps (3-pack) | $5.00 | $2.50-3.50 | 30-50% |
| Bamboo toothbrush (4-pack) | $3.50 | $1.50-2.50 | 29-57% |
| Silicone storage bags (6-pack) | $6.00 | $3.00-4.50 | 25-50% |
| Gooseneck kettle | $22.00 | $12.00-16.00 | 27-45% |

**Typical Alibaba MOQs for Eco Products:**
- Small items (brushes, straws): 200-500 units
- Medium items (wraps, bags): 100-300 units
- Large items (kettles, teapots): 50-200 units

**Investment Required:** $500-1,500 for initial inventory of top sellers

### Inventory Storage During Phase 2

**Option A: CJ US Warehouse**
- Store direct-sourced inventory at CJ's US facility
- CJ handles picking, packing, shipping
- 90 days free storage, then ~$0.50-1.00/cubic foot/month
- Good for: Testing direct sourcing without 3PL commitment

**Option B: Self-Storage (if local)**
- Rent small storage unit ($50-100/month)
- Self-fulfill or use USPS/UPS
- Good for: Very small quantities, need full control
- Not recommended long-term (time-intensive)

---

## Phase 3: 3PL Fulfillment (Months 9+)

### When to Switch to 3PL

Move to a dedicated 3PL when:
- 200+ orders/month consistently
- Margins support $3-5/order fulfillment fee
- Need 2-3 day shipping to compete
- Self-fulfillment or CJ shipping times become a bottleneck

### Recommended 3PLs

**ShipBob**
| Feature | Details |
|---------|---------|
| Setup fee | $0 (no setup fee) |
| Receiving | $25-35 per man-hour |
| Storage | $5 per bin/month, $10 per shelf/month, $40 per pallet/month |
| Pick & pack | $0 first pick, $0.20 per additional |
| Shipping | Discounted carrier rates (USPS, UPS, FedEx, DHL) |
| Best for | 200-10,000+ orders/month |
| Fulfillment centers | 40+ US + international |

**ShipMonk**
| Feature | Details |
|---------|---------|
| Setup fee | $0 |
| Storage | $1/small bin, $2/medium bin, $4/large bin per month |
| Pick & pack | Included in base rate |
| Shipping | $3-5 for lightweight orders |
| Best for | 100-5,000 orders/month |
| Fulfillment centers | US (Florida, California, Nevada, Pennsylvania) |

**Recommendation:** ShipBob for long-term growth (more warehouses, better integration options). ShipMonk as backup if ShipBob pricing doesn't work.

### Phase 3 Cost Per Order Example

**Example: Beeswax Wraps 3-Pack (Direct Sourced + 3PL)**
| Component | Cost |
|-----------|------|
| Product (Alibaba direct) | $3.00 |
| 3PL pick & pack | $2.50 |
| Shipping (USPS discounted) | $3.50 |
| Stripe fee (2.9% + $0.30 on $18 sale) | $0.82 |
| Storage (allocated per unit) | $0.15 |
| **Total COGS** | **$9.97** |
| **Retail price** | **$18.00** |
| **Gross profit** | **$8.03 (45%)** |

**vs Phase 1 profit: $7.68 (43%) → Phase 3: $8.03 (45%)**

---

## Canadian Shipping Strategy

### US to Canada Shipping

**Challenges:**
- Customs clearance required on every shipment
- GST/HST collected at border (or pre-paid)
- Longer delivery times (7-14 days typical)
- Higher shipping costs ($8-15 per order)

**Options:**

| Method | Cost | Delivery | Duties Handling |
|--------|------|----------|-----------------|
| USPS First Class International | $8-14 | 7-14 days | Buyer pays at door (DAP) |
| UPS Standard to Canada | $12-18 | 5-8 days | Can pre-pay (DDP) |
| CJ Canada Warehouse | $4-7 | 3-7 days | Pre-cleared |
| ShipBob Canadian fulfillment | $3-5 | 2-5 days | Pre-cleared |

**Recommended Approach:**
1. **Phase 1:** Ship USPS to Canada with clear "duties may apply" messaging
2. **Phase 2:** If >20 Canadian orders/month, explore CJ Canada warehouse
3. **Phase 3:** Add ShipBob Canadian fulfillment center

### Duty Considerations

- Most eco-friendly products fall under HS codes with low duty rates (0-8%)
- De minimis threshold for Canada: CAD $20 (very low - most orders will incur duty)
- GST (5%) applies on all imports, plus provincial tax (varies 0-10%)
- **Recommendation:** Price Canadian orders 10-15% higher to absorb duty, or offer DDP

### Canadian Pricing Strategy

**Option A: Separate CAD Pricing (Recommended for Phase 2+)**
- Display prices in CAD with duties included
- Mark up 15-20% over USD price
- Transparent "all-in pricing" builds trust

**Option B: USD Pricing with Duty Warning (Phase 1)**
- Keep prices in USD
- Clear notice: "Canadian customers: Import duties and taxes may apply at delivery"
- Simpler to implement, less customer-friendly

---

## Packaging & Unboxing Experience

### Phase 1: Minimal Branded Packaging

| Item | Cost | Source |
|------|------|--------|
| Branded sticker (logo seal) | $0.05-0.10 | Printful or sticker supplier |
| Branded thank-you card | $0.15-0.25 | Printful or local printer |
| Tissue paper (recycled kraft) | $0.10-0.15 | EcoEnclose |
| Shipping box/mailer | Included by CJ | CJ standard packaging |

**Cost per order: ~$0.30-0.50**

### Phase 2: Enhanced Branded Experience

| Item | Cost | Source |
|------|------|--------|
| Custom branded mailer (kraft) | $0.50-1.00 | EcoEnclose custom |
| Branded tissue paper | $0.15-0.25 | EcoEnclose |
| Thank-you card + discount code | $0.15-0.25 | Printful |
| Sticker pack (2-3 stickers) | $0.10-0.20 | Sticker supplier |
| Sustainability info card | $0.10-0.15 | Local printer |

**Cost per order: ~$1.00-1.85**

### Phase 3: Premium Unboxing

| Item | Cost | Source |
|------|------|--------|
| Custom branded box (kraft/recycled) | $1.00-2.00 | EcoEnclose |
| Custom tissue paper | $0.20-0.30 | EcoEnclose |
| Branded packing tape | $0.10-0.15 | EcoEnclose |
| Premium thank-you card | $0.20-0.30 | Printful |
| Product care cards | $0.10-0.15 | Local printer |
| Free sample of new product | $0.50-1.00 | Inventory |

**Cost per order: ~$2.10-3.90**

---

## Returns & Refund Policy

### Policy Framework

| Scenario | Policy | Notes |
|----------|--------|-------|
| Defective product | Full refund + free replacement | Don't require return (shipping cost > product value for most items) |
| Wrong item sent | Full refund + correct item sent | Investigate supplier error |
| Customer changed mind | 30-day return, customer pays shipping | Restocking only if opened/used |
| Item damaged in transit | Full refund or replacement | File carrier claim |

### Return Cost Management

For most eco-friendly products ($10-25 retail), it's cheaper to let the customer keep the item and send a replacement than to pay for return shipping:

| Approach | Cost | Customer Satisfaction |
|----------|------|---------------------|
| Full refund, keep item | Product wholesale cost ($3-8) | Very High |
| Send replacement, keep defective | Product wholesale cost x2 ($6-16) | Very High |
| Require return + refund | Return shipping ($5-8) + processing time | Medium |

**Recommendation:** For items under $25, offer refund/replacement without return. Above $25, case-by-case.

**Expected Return Rate:** 3-5% for home goods (industry average)

---

## Fulfillment KPIs to Track

| Metric | Target (Phase 1) | Target (Phase 3) |
|--------|-------------------|-------------------|
| Order processing time | < 48 hours | < 24 hours |
| Average shipping time (US) | 7-12 days | 3-5 days |
| Average shipping time (Canada) | 12-18 days | 5-8 days |
| Shipping cost as % of revenue | < 15% | < 10% |
| Return rate | < 5% | < 3% |
| Customer satisfaction (shipping) | > 4.0/5 | > 4.5/5 |
| Inventory accuracy | N/A (dropship) | > 98% |
| Stockout rate | N/A (dropship) | < 2% |

---

## Fulfillment Integration Priority

| Integration | Phase | Complexity | Impact |
|-------------|-------|------------|--------|
| CJDropshipping API | 1 | Medium | Critical - automates order fulfillment |
| Printful API | 1 | Low | Important - branded products |
| Shipping notification emails | 1 | Low | Critical - customer experience |
| Order tracking page | 1 | Low | Important - reduces support tickets |
| ShipBob API | 3 | Medium | Important - scales fulfillment |
| Inventory sync | 2-3 | Medium | Important - prevents overselling |

See [05-TECH-INTEGRATION.md](05-TECH-INTEGRATION.md) for detailed API integration plans.

---

*All supplier pricing is approximate and should be verified with current quotes. Request samples before committing to any supplier relationship.*
