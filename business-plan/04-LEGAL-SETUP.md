# 04 - Legal Setup

**Last Updated:** February 12, 2026

---

## LLC Formation

### State Selection

| State | Filing Fee | Annual Fee | Advantages | Disadvantages |
|-------|-----------|-----------|------------|---------------|
| **Wyoming** | $100 | $60/yr license tax | No state income tax, strong privacy, low fees | Need registered agent ($50-200/yr) if not resident |
| Delaware | $90 | $300/yr franchise tax | Business-friendly courts | Higher annual costs, foreign LLC registration needed |
| New Mexico | $50 | $0/yr | Cheapest option, no annual report | Less business-friendly reputation |
| Home State | Varies | Varies | Simplest compliance, no foreign LLC needed | May have higher taxes/fees |

**Recommendation:** Form in your **home state** unless you have a specific reason not to. Forming in Wyoming/Delaware when you operate from another state means you'll need to register as a foreign LLC in your home state anyway (additional cost + complexity).

If home state has high fees (e.g., California $800/yr), then Wyoming ($100 + $60/yr) becomes attractive.

### LLC Formation Checklist

- [ ] **Choose business name** (see [07-BRAND-DEVELOPMENT.md](07-BRAND-DEVELOPMENT.md))
- [ ] **Check name availability** in chosen state (secretary of state website)
- [ ] **File Articles of Organization** with state ($50-300 depending on state)
- [ ] **Get EIN (Employer Identification Number)** from IRS (free, irs.gov)
- [ ] **Draft Operating Agreement** (single-member template, keeps personal liability separate)
- [ ] **Appoint Registered Agent** (can be yourself if in-state, or service for $50-200/yr)
- [ ] **Register for state taxes** if required (income tax, sales tax)

**Timeline:** 1-3 business days (most states offer online filing)

**Cost Breakdown:**
| Item | Cost | Frequency |
|------|------|-----------|
| Articles of Organization | $50-300 | One-time |
| Operating Agreement template | $0-50 | One-time (free templates available) |
| EIN from IRS | $0 | One-time |
| Registered Agent (if needed) | $50-200 | Annual |
| **Total Year 1** | **$100-550** | |
| **Total Annual (ongoing)** | **$50-260** | Depends on state |

### Avoid These Mistakes

1. **Don't skip the Operating Agreement** - Even single-member LLCs need one to maintain liability protection
2. **Don't commingle personal and business funds** - Always use separate bank account
3. **Don't ignore foreign LLC registration** - If you form in one state but operate from another, register in both
4. **Don't use personal credit cards** for business expenses - Creates liability piercing risk

---

## Business Bank Account

### Recommended Banks for E-Commerce

| Bank | Monthly Fee | Features | Best For |
|------|-----------|----------|----------|
| **Mercury** | $0 | No fees, integrations, Stripe-friendly | Online-first business |
| Relay | $0 | Multiple accounts, profit-first budgeting | Cash flow management |
| Novo | $0 | Integrations with Stripe, Shopify | E-commerce |
| Chase Business | $15/mo (waivable) | Branch access, established name | Need physical branch |
| Bluevine | $0 | Interest on deposits (2%+) | Maximizing idle cash |

**Recommendation:** **Mercury** - Purpose-built for online businesses, no fees, excellent Stripe integration, easy ACH transfers.

### Bank Account Checklist

- [ ] Have EIN from IRS
- [ ] Have Articles of Organization filed
- [ ] Apply for business bank account
- [ ] Set up ACH transfers from Stripe to business account
- [ ] Get business debit card for expenses
- [ ] Set up accounting categories (QuickBooks Self-Employed or Wave - both free/cheap)

---

## Sales Tax

### Overview

Since the 2018 South Dakota v. Wayfus (Wayfair) Supreme Court decision, states can require online sellers to collect sales tax even without physical presence, based on "economic nexus" thresholds.

### Economic Nexus Thresholds (2026)

**Most Common Threshold:** $100,000 in sales OR 200 transactions in the state per year.

| Threshold Type | States |
|----------------|--------|
| $100,000 revenue only | Most states (trend is removing transaction threshold) |
| $100,000 OR 200 transactions | Declining (many states removing transaction count) |
| $500,000 + 100 transactions | New York |
| No sales tax | Alaska*, Delaware, Montana, New Hampshire, Oregon |

*Alaska has no state sales tax but some local jurisdictions do.

**2026 Changes:**
- Illinois eliminated its 200-transaction rule effective January 1, 2026 (now $100,000 only)
- Utah eliminated its 200-transaction threshold effective July 1, 2025

### When Do You Need to Collect Sales Tax?

**Phase 1 (0-50 orders):** You likely won't hit nexus in any state. Monitor sales by state.

**Phase 2 (50-200 orders):** You may approach nexus in 1-2 high-volume states. Register when you approach thresholds.

**Phase 3 (200+ orders):** Register in all states where you have nexus.

### Sales Tax Compliance Options

| Service | Monthly Cost | What It Does |
|---------|-------------|-------------|
| **TaxJar** | $19-99/mo | Calculate, collect, file returns automatically |
| Avalara | Custom pricing | Enterprise-grade, API integration |
| TaxCloud | Free | Free calculation, filing for extra fee |
| Manual filing | $0 | DIY per state (time-intensive) |

**Recommendation:**
- **Phase 1:** Track manually. You won't hit nexus early.
- **Phase 2:** Sign up for TaxJar ($19/mo) when approaching nexus in first state.
- **Phase 3:** TaxJar or Avalara for multi-state compliance.

### Sales Tax Implementation in Laravel

The existing app has tax calculation built in (6.5% flat rate in OrderFactory). This will need to be updated to support per-state rates via TaxJar API. See [05-TECH-INTEGRATION.md](05-TECH-INTEGRATION.md).

---

## Canadian Tax Compliance

### GST/HST Requirements

If you sell to Canadian customers, Canadian tax rules apply:

| Province | Tax Type | Rate |
|----------|----------|------|
| Alberta | GST only | 5% |
| British Columbia | GST + PST | 5% + 7% = 12% |
| Ontario | HST | 13% |
| Quebec | GST + QST | 5% + 9.975% = 14.975% |
| Other provinces | Varies | 5-15% |

### When Must You Register?

**As a non-resident (US-based) vendor selling to Canada:**
- If total revenue from Canadian sales exceeds CAD $30,000 over 12 months, you must register for GST/HST
- Registration is with the Canada Revenue Agency (CRA)

**Phase 1 Strategy:**
- You'll be far below the threshold initially
- Ship prices in USD, customer pays duties/GST at border
- No registration needed until CAD $30K threshold

**Phase 2+ Strategy:**
- Monitor Canadian sales volume
- When approaching CAD $30K, register for GST/HST
- Consider charging Canadian tax at checkout for better customer experience

---

## Stripe Configuration

### Live Account Setup Checklist

- [ ] Verify Stripe account is activated for live payments
- [ ] Complete identity verification (upload ID + selfie)
- [ ] Add business information (LLC name, EIN, address)
- [ ] Connect business bank account for payouts
- [ ] Configure payout schedule (daily or weekly)
- [ ] Enable Stripe Radar for fraud protection
- [ ] Set up webhook endpoints for the new production app
- [ ] Test a real $1 transaction and refund

### Stripe Fees Summary

| Transaction Type | Fee |
|-----------------|-----|
| Domestic card payment | 2.9% + $0.30 |
| International card | 3.9% + $0.30 (+1% currency conversion) |
| ACH direct debit | 0.8% (max $5) |
| Disputes/chargebacks | $15 per dispute |
| Refunds | Original fee not returned (you lose the processing fee) |

### Fraud Protection

- Enable **Stripe Radar** (free for basic rules, $0.02/transaction for Radar for Fraud Teams)
- Set up rules:
  - Block payments from high-risk countries (if not shipping there)
  - Require CVC match
  - Block disposable email addresses
  - Review orders over $200

---

## Required Legal Pages

### 1. Privacy Policy

**Must Include:**
- What data you collect (name, email, address, payment info)
- How you use the data (order fulfillment, marketing)
- Third parties who receive data (Stripe, fulfillment partners, email service)
- Cookie usage and tracking (Google Analytics, Meta Pixel)
- Customer rights (access, delete, opt-out)
- CCPA compliance (California Consumer Privacy Act)
- Contact information

**Generation Options:**
- Free: Termly.io, PrivacyPolicies.com (generate basic policy)
- Paid: Have a lawyer review ($200-500 one-time)
- DIY: Use template and customize for your business

### 2. Terms of Service

**Must Include:**
- Acceptance of terms
- Account creation and responsibilities
- Ordering process and pricing
- Payment terms
- Shipping and delivery policy
- Returns and refunds policy
- Limitation of liability
- Governing law (your state)

### 3. Return & Refund Policy

**Recommended Policy:**
```
30-Day Return Policy
- Unopened items: Full refund, customer pays return shipping
- Defective items: Full refund + free replacement, no return required
- Items under $25 with issues: Refund/replacement without return
- Gift cards and sale items: Final sale
- Process: Email support@[domain].com with order number and issue
- Refund timeline: 5-10 business days after approval
```

### 4. Shipping Policy

**Must Include:**
- Processing time (1-3 business days)
- Domestic shipping rates and timeframes
- International/Canada shipping info
- Free shipping threshold ($45+)
- Tracking information
- Lost/damaged package policy

### 5. Cookie Policy (if using analytics/tracking)

**Must Include:**
- What cookies are used
- Purpose (analytics, advertising, functionality)
- How to disable cookies
- Cookie consent banner (required in some jurisdictions)

---

## Product Compliance

### General Product Safety

For eco-friendly home products, verify:
- [ ] Products comply with CPSC (Consumer Product Safety Commission) standards
- [ ] No products on CPSC recall list
- [ ] Food-contact products (wraps, containers, utensils) are FDA-compliant
- [ ] Products have required safety warnings/labels
- [ ] Eco claims are substantiated (avoid "greenwashing")

### FTC Green Guides Compliance

The FTC regulates environmental marketing claims. Key rules:
- **"Eco-friendly"** - Must be specific about what makes it eco-friendly
- **"Biodegradable"** - Must decompose within 1 year in normal disposal
- **"Compostable"** - Must break down in home or commercial compost
- **"Recyclable"** - Must be accepted by most recycling programs
- **"Made from recycled materials"** - Must specify percentage

**Best Practice:** Be specific in claims. Instead of "eco-friendly," say "made from organic cotton" or "replaces 1,000 plastic bags."

### Proposition 65 (California)

If selling to California customers:
- Products containing chemicals on Prop 65 list must include warning
- Applies to items with lead, cadmium, BPA, etc.
- Most eco-friendly products are naturally compliant
- Check with suppliers about Prop 65 compliance

---

## Insurance

### Business Insurance Options

| Type | Cost/Year | What It Covers | When Needed |
|------|----------|---------------|-------------|
| General Liability | $300-600 | Product liability, bodily injury | Phase 1 (recommended) |
| Product Liability | Included in GL | Defective product claims | Phase 1 (recommended) |
| Cyber Insurance | $200-500 | Data breach, hacking | Phase 2 |
| Business Property | $200-400 | Inventory, equipment | Phase 3 (when holding inventory) |

**Recommendation:**
- **Phase 1:** General liability policy ($300-600/yr) covers most risks
- **Phase 2:** Add cyber insurance if storing customer payment data
- **Phase 3:** Add property insurance when holding inventory

**Where to Get:**
- Hiscox (online business insurance, instant quotes)
- Next Insurance (small business focused)
- State Farm / GEICO (if you prefer traditional)

---

## Compliance Checklist (Quick Reference)

### Before Launch
- [ ] LLC formed and operating agreement signed
- [ ] EIN obtained from IRS
- [ ] Business bank account opened
- [ ] Stripe live account configured
- [ ] Privacy Policy published
- [ ] Terms of Service published
- [ ] Return/Refund Policy published
- [ ] Shipping Policy published
- [ ] Cookie consent banner implemented (if using tracking)
- [ ] General liability insurance obtained

### Monthly
- [ ] Track sales by state (for sales tax nexus monitoring)
- [ ] Track Canadian sales (for GST/HST threshold)
- [ ] Reconcile Stripe payouts with bank deposits
- [ ] Review chargebacks/disputes

### Quarterly
- [ ] File sales tax returns (when registered)
- [ ] Review LLC compliance (annual report deadlines vary by state)
- [ ] Update policies if business practices change

### Annually
- [ ] File LLC annual report (if required by state)
- [ ] Pay LLC annual fee/franchise tax
- [ ] File business income taxes (Schedule C or LLC tax return)
- [ ] Review and renew business insurance
- [ ] Review sales tax nexus in all states

---

## Estimated Legal Setup Costs

| Item | One-Time | Annual |
|------|----------|--------|
| LLC Formation | $100-300 | - |
| Registered Agent | - | $50-200 |
| LLC Annual Fee | - | $0-300 (state dependent) |
| EIN | $0 | - |
| Business Bank Account | $0 | $0 |
| Legal Page Templates | $0-100 | - |
| General Liability Insurance | - | $300-600 |
| TaxJar (when needed) | - | $228-1,188 ($19-99/mo) |
| **Total Year 1** | **$100-400** | **$350-1,100** |
| **Total Ongoing (Annual)** | - | **$350-1,100** |

---

*This document provides general guidance. Consult with a business attorney and CPA for advice specific to your jurisdiction and circumstances. Tax laws and thresholds change frequently - verify current requirements before filing.*
