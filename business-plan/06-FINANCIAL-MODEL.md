# 06 - Financial Model

**Last Updated:** February 12, 2026

---

## Key Assumptions

| Variable | Value | Source/Rationale |
|----------|-------|-----------------|
| Average order value (AOV) | $38 | Eco products $15-25 + coffee/tea $35-65, bundles lift AOV |
| Average product cost (COGS %) | 35% | Weighted average across product categories |
| Shipping cost per order | $4.50 | CJ US warehouse average |
| Stripe fee per order | 2.9% + $0.30 | Standard rate for domestic cards |
| Packaging cost per order | $0.50 | Phase 1 branded insert |
| Website conversion rate | 2.0% | E-commerce average for niche DTC |
| Email conversion rate | 3.5% | Higher intent from owned audience |
| Return rate | 4% | Home goods industry average |
| Customer acquisition cost (CAC) | $15 | Blended across paid + organic channels |
| Repeat purchase rate | 20% (mo 6), 30% (mo 12) | Eco consumables drive repeat |
| Average orders per repeat customer/year | 2.5 | Consumable replenishment cycle |

---

## Monthly Revenue Projections (Month 1-12)

### Conservative Scenario

| Month | Orders | Revenue | COGS | Gross Profit | Marketing | Net Profit |
|-------|--------|---------|------|-------------|-----------|------------|
| 1 | 8 | $304 | $106 | $198 | $200 | -$2 |
| 2 | 15 | $570 | $200 | $370 | $300 | $70 |
| 3 | 25 | $950 | $333 | $618 | $400 | $218 |
| 4 | 35 | $1,330 | $466 | $864 | $500 | $364 |
| 5 | 50 | $1,900 | $665 | $1,235 | $600 | $635 |
| 6 | 65 | $2,470 | $865 | $1,605 | $700 | $905 |
| 7 | 80 | $3,040 | $1,064 | $1,976 | $750 | $1,226 |
| 8 | 95 | $3,610 | $1,264 | $2,347 | $800 | $1,547 |
| 9 | 110 | $4,180 | $1,463 | $2,717 | $850 | $1,867 |
| 10 | 125 | $4,750 | $1,663 | $3,088 | $900 | $2,188 |
| 11 | 145 | $5,510 | $1,929 | $3,581 | $1,000 | $2,581 |
| 12 | 160 | $6,080 | $2,128 | $3,952 | $1,000 | $2,952 |
| **Year 1** | **913** | **$34,694** | **$12,143** | **$22,551** | **$8,000** | **$14,551** |

### Moderate Scenario

| Month | Orders | Revenue | COGS | Gross Profit | Marketing | Net Profit |
|-------|--------|---------|------|-------------|-----------|------------|
| 1 | 12 | $456 | $160 | $296 | $250 | $46 |
| 2 | 25 | $950 | $333 | $618 | $400 | $218 |
| 3 | 40 | $1,520 | $532 | $988 | $500 | $488 |
| 4 | 60 | $2,280 | $798 | $1,482 | $650 | $832 |
| 5 | 80 | $3,040 | $1,064 | $1,976 | $800 | $1,176 |
| 6 | 100 | $3,800 | $1,330 | $2,470 | $900 | $1,570 |
| 7 | 120 | $4,560 | $1,596 | $2,964 | $1,000 | $1,964 |
| 8 | 140 | $5,320 | $1,862 | $3,458 | $1,100 | $2,358 |
| 9 | 165 | $6,270 | $2,195 | $4,076 | $1,200 | $2,876 |
| 10 | 190 | $7,220 | $2,527 | $4,693 | $1,300 | $3,393 |
| 11 | 220 | $8,360 | $2,926 | $5,434 | $1,500 | $3,934 |
| 12 | 250 | $9,500 | $3,325 | $6,175 | $1,500 | $4,675 |
| **Year 1** | **1,402** | **$53,276** | **$18,647** | **$34,629** | **$11,100** | **$23,529** |

### Aggressive Scenario (Best Case)

| Month | Orders | Revenue | COGS | Gross Profit | Marketing | Net Profit |
|-------|--------|---------|------|-------------|-----------|------------|
| 1 | 20 | $760 | $266 | $494 | $350 | $144 |
| 2 | 40 | $1,520 | $532 | $988 | $500 | $488 |
| 3 | 70 | $2,660 | $931 | $1,729 | $700 | $1,029 |
| 4 | 100 | $3,800 | $1,330 | $2,470 | $900 | $1,570 |
| 5 | 140 | $5,320 | $1,862 | $3,458 | $1,100 | $2,358 |
| 6 | 180 | $6,840 | $2,394 | $4,446 | $1,300 | $3,146 |
| 7 | 220 | $8,360 | $2,926 | $5,434 | $1,500 | $3,934 |
| 8 | 260 | $9,880 | $3,458 | $6,422 | $1,700 | $4,722 |
| 9 | 300 | $11,400 | $3,990 | $7,410 | $1,900 | $5,510 |
| 10 | 340 | $12,920 | $4,522 | $8,398 | $2,000 | $6,398 |
| 11 | 380 | $14,440 | $5,054 | $9,386 | $2,200 | $7,186 |
| 12 | 420 | $15,960 | $5,586 | $10,374 | $2,400 | $7,974 |
| **Year 1** | **2,470** | **$93,860** | **$32,851** | **$61,009** | **$16,550** | **$44,459** |

---

## Fixed Costs (Monthly)

| Expense | Phase 1 | Phase 2 | Phase 3 | Notes |
|---------|---------|---------|---------|-------|
| Domain + DNS | $2 | $2 | $2 | Annual cost amortized |
| Hosting (VPS) | $12-24 | $12-24 | $20-40 | DigitalOcean / Hetzner |
| Professional email | $6 | $6 | $6 | Google Workspace basic |
| SSL certificate | $0 | $0 | $0 | Let's Encrypt (free) |
| LLC annual fee (amortized) | $5-25 | $5-25 | $5-25 | State dependent |
| Insurance (amortized) | $25-50 | $25-50 | $25-50 | General liability |
| TaxJar | $0 | $19 | $19-99 | When sales tax nexus reached |
| Accounting software | $0 | $0-15 | $15-30 | Wave (free) → QuickBooks |
| Design tools (Canva) | $0-13 | $13 | $13 | Canva Pro for social graphics |
| **Total Fixed/Month** | **$50-120** | **$82-154** | **$105-265** | |

---

## Variable Costs Per Order

| Cost Component | Amount | % of $38 AOV |
|----------------|--------|-------------|
| Product cost (wholesale) | $13.30 | 35.0% |
| Shipping to customer | $4.50 | 11.8% |
| Stripe processing (2.9% + $0.30) | $1.40 | 3.7% |
| Packaging insert | $0.50 | 1.3% |
| Returns/refunds (4% rate) | $0.61 | 1.6% |
| **Total variable per order** | **$20.31** | **53.4%** |
| **Gross profit per order** | **$17.69** | **46.6%** |

### Variable Cost by Phase

| Phase | Product Cost | Shipping | Processing | Packaging | Gross Margin |
|-------|-------------|----------|------------|-----------|-------------|
| Phase 1 (Dropship) | 35% | 11.8% | 3.7% | 1.3% | 44-48% |
| Phase 2 (Hybrid) | 25-30% | 10% | 3.7% | 2.5% | 50-55% |
| Phase 3 (3PL) | 20-25% | 9% | 3.7% | 3% | 55-60% |

---

## Break-Even Analysis

### Monthly Break-Even (Fixed Costs Only)

| Scenario | Fixed Costs/Mo | Gross Profit/Order | Orders to Break Even | Revenue to Break Even |
|----------|---------------|-------------------|---------------------|----------------------|
| Phase 1 (lean) | $75 | $17.69 | 5 orders | $190 |
| Phase 1 (normal) | $100 | $17.69 | 6 orders | $228 |
| Phase 2 | $130 | $19.50 | 7 orders | $266 |
| Phase 3 | $200 | $22.00 | 10 orders | $380 |

### Break-Even Including Marketing Spend

| Monthly Marketing | Fixed Costs | Total Monthly Cost | Orders Needed | Revenue Needed |
|-------------------|-------------|-------------------|---------------|----------------|
| $200 | $100 | $300 | 17 | $646 |
| $500 | $100 | $600 | 34 | $1,292 |
| $750 | $130 | $880 | 46 | $1,748 |
| $1,000 | $130 | $1,130 | 58 | $2,204 |
| $1,500 | $200 | $1,700 | 78 | $2,964 |

### Startup Investment Recovery

| Scenario | Total Startup Spend | Monthly Net Profit (Mo 6) | Months to Recover |
|----------|--------------------|--------------------------|--------------------|
| Conservative | $2,000 | $905 | ~5 months (month 8 from start) |
| Moderate | $3,000 | $1,570 | ~4 months (month 7 from start) |
| Aggressive | $4,000 | $3,146 | ~3 months (month 5 from start) |

---

## Customer Lifetime Value (CLV)

### CLV Calculation

| Variable | Value | Notes |
|----------|-------|-------|
| Average order value | $38 | Across all products |
| Average orders per year | 2.5 | Eco consumables drive repeat |
| Gross margin per order | $17.69 | After COGS, shipping, processing |
| Average customer lifespan | 2 years | Conservative estimate |
| **CLV (Revenue)** | **$190** | $38 x 2.5 x 2 |
| **CLV (Gross Profit)** | **$88** | $17.69 x 2.5 x 2 |

### CLV:CAC Ratio

| Phase | CAC | CLV (Profit) | Ratio | Health |
|-------|-----|-------------|-------|--------|
| Phase 1 | $20 | $88 | 4.4:1 | Healthy (>3:1 is good) |
| Phase 2 | $15 | $95 | 6.3:1 | Strong |
| Phase 3 | $12 | $110 | 9.2:1 | Excellent |

**Target:** CLV:CAC ratio > 3:1 (rule of thumb for sustainable e-commerce)

---

## Startup Budget Allocation

### Total Budget: $3,000 (Recommended Starting Point)

| Category | Amount | % of Budget | Timing |
|----------|--------|-------------|--------|
| Legal setup (LLC, EIN) | $200 | 7% | Month 1 |
| Domain + branding | $100 | 3% | Month 1 |
| Product samples (15 products) | $250 | 8% | Month 1 |
| Product photography | $150 | 5% | Month 1-2 |
| Paid ads (months 1-3) | $900 | 30% | Month 1-3 |
| Influencer budget | $400 | 13% | Month 3-4 |
| Insurance (6 months) | $250 | 8% | Month 1 |
| Tools & subscriptions | $150 | 5% | Month 1-3 |
| Content creation | $200 | 7% | Month 1-4 |
| Emergency reserve | $400 | 13% | Hold |
| **Total** | **$3,000** | **100%** | |

### Minimum Viable Budget: $1,500

| Category | Amount | Notes |
|----------|--------|-------|
| LLC + EIN | $150 | Bare minimum legal |
| Domain | $15 | Domain registration |
| Product samples (10 products) | $150 | Fewer products to test |
| Paid ads (months 1-3) | $450 | $5/day for 90 days |
| Content/photography | $100 | DIY with smartphone |
| Insurance | $250 | General liability (6 months) |
| Reserve | $385 | Emergency buffer |
| **Total** | **$1,500** | |

---

## Profit & Loss Summary (Year 1)

### Moderate Scenario

| Line Item | Amount | % of Revenue |
|-----------|--------|-------------|
| **Revenue** | **$53,276** | 100% |
| Product costs (COGS) | -$18,647 | -35.0% |
| Shipping costs | -$6,309 | -11.8% |
| Payment processing | -$1,845 | -3.5% |
| Packaging | -$701 | -1.3% |
| Returns/refunds | -$852 | -1.6% |
| **Gross Profit** | **$24,922** | **46.8%** |
| Marketing (ads + influencers) | -$11,100 | -20.8% |
| Fixed costs (hosting, tools, etc.) | -$1,584 | -3.0% |
| Insurance | -$500 | -0.9% |
| Legal/accounting | -$300 | -0.6% |
| **Net Profit (Pre-Tax)** | **$11,438** | **21.5%** |

### Key Ratios

| Metric | Value | Industry Benchmark |
|--------|-------|--------------------|
| Gross margin | 46.8% | 40-60% (DTC e-commerce) |
| Net margin | 21.5% | 10-20% (healthy for year 1) |
| Marketing as % of revenue | 20.8% | 15-30% (growth phase) |
| Fixed costs as % of revenue | 4.5% | 5-15% (lean operation) |

---

## Sensitivity Analysis

### What If AOV Changes?

| AOV | Orders/Mo (Mo 12) | Monthly Revenue | Gross Profit/Order | Break-Even Orders |
|-----|--------------------|-----------------|--------------------|-------------------|
| $28 | 250 | $7,000 | $12.60 | 8 |
| $33 | 250 | $8,250 | $15.15 | 7 |
| **$38** | **250** | **$9,500** | **$17.69** | **6** |
| $43 | 250 | $10,750 | $20.24 | 5 |
| $48 | 250 | $12,000 | $22.78 | 5 |

### What If Conversion Rate Changes?

| Conversion Rate | Monthly Sessions (Mo 12) | Orders/Mo | Monthly Revenue |
|-----------------|--------------------------|-----------|-----------------|
| 1.0% | 25,000 | 250 | $9,500 |
| 1.5% | 16,667 | 250 | $9,500 |
| **2.0%** | **12,500** | **250** | **$9,500** |
| 2.5% | 10,000 | 250 | $9,500 |
| 3.0% | 8,333 | 250 | $9,500 |

*Higher conversion = need less traffic for same revenue = lower CAC*

### What If COGS Changes? (Direct Sourcing Impact)

| COGS % | Product Cost/Order | Gross Profit/Order | Gross Margin | Annual Profit Impact |
|--------|--------------------|--------------------|-------------|---------------------|
| 40% | $15.20 | $15.79 | 41.6% | -$2,667 vs baseline |
| **35%** | **$13.30** | **$17.69** | **46.6%** | **Baseline** |
| 30% | $11.40 | $19.59 | 51.6% | +$2,667 |
| 25% | $9.50 | $21.49 | 56.6% | +$5,334 |
| 20% | $7.60 | $23.39 | 61.6% | +$8,001 |

*Moving from 35% to 25% COGS (direct sourcing) adds ~$5,300/yr in profit at moderate volume*

---

## Cash Flow Considerations

### Monthly Cash Flow (Moderate Scenario)

| Month | Revenue | Total Costs | Net Cash | Cumulative |
|-------|---------|------------|----------|------------|
| 0 (setup) | $0 | -$650 | -$650 | -$650 |
| 1 | $456 | -$610 | -$154 | -$804 |
| 2 | $950 | -$933 | $17 | -$787 |
| 3 | $1,520 | -$1,232 | $288 | -$499 |
| 4 | $2,280 | -$1,648 | $632 | $133 |
| 5 | $3,040 | -$2,064 | $976 | $1,109 |
| 6 | $3,800 | -$2,430 | $1,370 | $2,479 |

**Cash-flow positive by month 4** (moderate scenario)

### Stripe Payout Timing

- Stripe holds funds for 7 days for new accounts (initial)
- After history builds, moves to 2-day rolling payouts
- Plan for 7-14 day float on initial revenue
- Never count pending Stripe balance as available cash

---

## Year 2 Projections (If Year 1 Succeeds)

| Metric | Year 1 (Moderate) | Year 2 (Projected) | Growth |
|--------|-------------------|---------------------|--------|
| Monthly orders (Dec) | 250 | 500 | 100% |
| Monthly revenue (Dec) | $9,500 | $21,000 | 121% |
| Annual revenue | $53,276 | $140,000 | 163% |
| Gross margin | 46.8% | 55% | +8pts |
| Net profit | $11,438 | $45,000 | 293% |
| Email subscribers | 3,000 | 10,000 | 233% |

**Year 2 levers:**
- Direct sourcing reduces COGS from 35% to 25%
- Email marketing drives 25-30% of revenue (free channel)
- Organic SEO traffic reduces blended CAC
- Repeat customers increase (30%+ rate)
- Potential to add subscription model for consumables

---

## Financial Decision Points

| Revenue/Month | Decision | Action |
|---------------|----------|--------|
| $500 | Is this working? | If CAC > $25 consistently, reassess niche/products |
| $1,000 | Double down | Increase ad budget to $500-750/mo, add influencers |
| $2,000 | Optimize margins | Start direct sourcing for top 5 products |
| $3,000 | Scale operations | Consider VA for customer service ($500-800/mo) |
| $5,000 | Infrastructure | Move to 3PL, professional bookkeeping |
| $10,000 | Maturity | Consider inventory investment, private label |

---

*All projections are estimates based on industry benchmarks. Actual results will vary. Review and update this model monthly with real data starting from month 1.*
