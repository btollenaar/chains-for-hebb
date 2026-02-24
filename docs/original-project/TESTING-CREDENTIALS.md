# Test Credentials & Testing Guide

## Overview
This document contains all test credentials for the multi-provider appointment system and step-by-step instructions for testing each role's functionality.

**Universal Password for All Test Accounts:** `password123`

---

## 🌐 Test Environment

### Live Test Site
**URL:** https://bentollenaar.dev
**Status:** ✅ Live and Functional
**Deployed:** December 16, 2025
**Purpose:** Client review and feature testing

### Environment Details
- **Hosting:** DreamHost Shared Hosting
- **PHP Version:** 8.4
- **Database:** MySQL (bentollenaar_dev)
- **Stripe Mode:** Test mode (use test cards)
- **Email:** Log only (no actual emails sent)

### Test Site Admin Access
- **Email:** admin@abettersolutionwellness.com
- **Password:** password
- **Role:** Admin
- **Access:** Full system access

**⚠️ Note:** The TestDataSeeder was not run on bentollenaar.dev, so only the basic admin account and sample data from CustomerSeeder exists. Provider and front desk test accounts can be created by admin if needed.

---

## Test Accounts by Role

### 🔧 Admin Account
- **Email:** michele@abettersolutionwellness.com
- **Password:** password123
- **Role:** Admin + Provider
- **Name:** Michele

**What to Test:**
- [ ] Admin dashboard access at `/admin`
- [ ] View all appointments (all providers)
- [ ] Filter appointments by provider
- [ ] Create appointments for customers (front desk booking)
- [ ] View/edit/delete any appointment
- [ ] Manage providers (CRUD operations)
- [ ] Manage services
- [ ] View provider dashboard at `/provider/dashboard`

---

### 👨‍⚕️ Provider Accounts

#### Provider 1: Dr. Sarah Johnson (Aesthetic Specialist)
- **Email:** sarah@abettersolutionwellness.com
- **Password:** password123
- **Role:** Provider
- **Specialties:** Botox & Dysport, Dermal Fillers, Chemical Peels, Facial Rejuvenation
- **Availability:** Monday, Wednesday, Friday (10:00 AM - 6:00 PM)
- **Services:** 4 aesthetic services

**What to Test:**
- [ ] Login and access provider dashboard at `/provider/dashboard`
- [ ] View today's appointments (should only see Sarah's appointments)
- [ ] View upcoming appointments for next 7 days
- [ ] View statistics cards (Today, This Week, Next 30 Days, Pending, Completed)
- [ ] Navigate to all appointments list at `/provider/appointments`
- [ ] Filter appointments by status
- [ ] Filter appointments by date range
- [ ] Search appointments by customer or service name
- [ ] View appointment details
- [ ] Public profile at `/team/dr-sarah-johnson`
- [ ] Verify only Mon/Wed/Fri time slots show when customers book

#### Provider 2: Dr. Alex Chen (IV Therapy Specialist)
- **Email:** alex@abettersolutionwellness.com
- **Password:** password123
- **Role:** Provider
- **Specialties:** IV Nutrient Therapy, NAD+ Optimization, Vitamin Therapy, Cellular Health
- **Availability:** Tuesday, Thursday, Saturday (9:00 AM - 5:00 PM)
- **Services:** 3 IV therapy services

**What to Test:**
- [ ] Login and access provider dashboard
- [ ] View only Alex's appointments (not other providers')
- [ ] Statistics are accurate for Alex's appointments only
- [ ] Public profile at `/team/dr-alex-chen`
- [ ] Verify only Tue/Thu/Sat time slots show when customers book
- [ ] Verify IV therapy services show in Alex's profile

#### Provider 3: Jessica Martinez (Wellness Consultant)
- **Email:** jessica@abettersolutionwellness.com
- **Password:** password123
- **Role:** Provider
- **Specialties:** Hormone Optimization, Medical Weight Loss, Nutrition Counseling, Lifestyle Medicine
- **Availability:** Monday, Tuesday, Wednesday, Thursday (11:00 AM - 7:00 PM)
- **Services:** 2 wellness services

**What to Test:**
- [ ] Login and access provider dashboard
- [ ] View only Jessica's appointments
- [ ] Public profile at `/team/jessica-martinez`
- [ ] Verify only Mon-Thu time slots show when customers book
- [ ] Verify 11am-7pm hours (later start time)

---

### 👔 Front Desk Staff Accounts

#### Front Desk 1: Emma Wilson (Receptionist)
- **Email:** emma@abettersolutionwellness.com
- **Password:** password123
- **Role:** Front Desk

**What to Test:**
- [ ] Login with front desk credentials
- [ ] Access admin appointment management at `/admin/appointments`
- [ ] Create new appointment for walk-in customer
- [ ] Select customer from dropdown
- [ ] Select service from dropdown
- [ ] Select provider from dropdown (should show all 4 providers)
- [ ] Select date
- [ ] See available time slots load via AJAX
- [ ] Book appointment successfully
- [ ] Add appointment notes
- [ ] View all appointments (all providers)
- [ ] Filter by provider
- [ ] Search for appointments
- [ ] View appointment details
- [ ] **Cannot access:** `/admin/providers` (provider management - admin only)
- [ ] **Cannot access:** `/admin/services` (service management - admin only)

#### Front Desk 2: David Thompson (Manager)
- **Email:** david@abettersolutionwellness.com
- **Password:** password123
- **Role:** Front Desk

**What to Test:**
- [ ] Same as Emma Wilson (same role permissions)
- [ ] Verify can book for any provider
- [ ] Verify can see all providers' appointments

---

### 👥 Test Customer Accounts

#### Customer 1: John Smith
- **Email:** john.smith@example.com
- **Password:** password123
- **Role:** Customer

#### Customer 2: Emily Davis
- **Email:** emily.davis@example.com
- **Password:** password123
- **Role:** Customer

#### Customer 3: Michael Brown
- **Email:** michael.brown@example.com
- **Password:** password123
- **Role:** Customer

#### Customer 4: Lisa Anderson
- **Email:** lisa.anderson@example.com
- **Password:** password123
- **Role:** Customer

#### Customer 5: Robert Taylor
- **Email:** robert.taylor@example.com
- **Password:** password123
- **Role:** Customer

**What to Test for All Customers:**
- [ ] Login with customer credentials
- [ ] Access customer dashboard
- [ ] View "My Appointments" page
- [ ] Book new appointment
- [ ] See all 4 providers in provider selection
- [ ] Different time slots for each provider based on their availability
- [ ] Verify provider selection updates available time slots
- [ ] Submit booking successfully
- [ ] View confirmation
- [ ] Cancel appointment
- [ ] **Cannot access:** `/admin/*` (admin routes)
- [ ] **Cannot access:** `/provider/dashboard` (provider routes)

---

## 🧪 Testing on bentollenaar.dev

### Available for Testing
✅ **Admin Panel:** Full access with admin@abettersolutionwellness.com / password
✅ **Product Management:** Add, edit, delete products
✅ **Service Management:** Configure services and availability
✅ **Order System:** Test checkout and payment (Stripe test mode)
✅ **Appointment Booking:** Book services and manage appointments
✅ **Blog System:** Create and publish blog posts
✅ **Customer Portal:** Register new customers and test dashboard

### Test Data Available
- **Admin Account:** 1 (admin@abettersolutionwellness.com)
- **Sample Products:** Seeded via ProductSeeder
- **Sample Services:** Seeded via ServiceSeeder
- **Sample Blog Posts:** Available for testing
- **Sample Orders:** Can be created during testing
- **Sample Customers:** Can register new accounts

### Features to Test
1. **E-Commerce Flow:**
   - Browse products at /products
   - Add to cart (test AJAX cart)
   - Checkout with Stripe test card: 4242 4242 4242 4242
   - View order confirmation
   - Check admin order management

2. **Service Booking:**
   - Browse services at /services
   - Book appointment (requires login/registration)
   - Select date and time
   - Confirm booking
   - View in admin appointments panel

3. **Admin Features:**
   - Dashboard at /admin
   - Product CRUD operations
   - Service CRUD operations
   - Order management
   - Appointment management
   - Blog management

4. **Customer Experience:**
   - Register new account at /register
   - Login at /login
   - View dashboard at /dashboard
   - Book appointment
   - View order history
   - Update profile

### Creating Test Accounts on bentollenaar.dev
Since TestDataSeeder wasn't run, you can create test accounts manually:

**Option 1: Register via UI** (for customers)
- Visit https://bentollenaar.dev/register
- Fill in registration form
- Email verification required

**Option 2: Create via Admin** (for providers/front desk)
1. Login as admin
2. Navigate to appropriate management section
3. Create new user account

**Option 3: Create via Tinker** (SSH access)
```bash
ssh btollenaar_dev@bentollenaar.dev
cd ~/bentollenaar.dev/app
php artisan tinker

# Create provider account
>>> $customer = Customer::create([
    'name' => 'Test Provider',
    'email' => 'testprovider@example.com',
    'password' => bcrypt('password123'),
    'role' => 'provider',
    'email_verified_at' => now()
]);
```

---

## Comprehensive Testing Checklist (Local Development)

### 1. Public Pages (No Login Required)

#### Team Page (`/team`)
- [ ] Visit `/team` without logging in
- [ ] Should see all 4 providers:
  - Michele (Founder & Lead Wellness Specialist)
  - Dr. Sarah Johnson (Aesthetic Specialist)
  - Dr. Alex Chen (IV Therapy Specialist)
  - Jessica Martinez (Wellness Consultant)
- [ ] Each provider card shows:
  - Profile image (if uploaded)
  - Name and title
  - Bio preview (truncated)
  - Services badges
  - "View Profile" button

#### Individual Provider Profiles (`/team/{slug}`)
- [ ] Click on each provider to view their profile
- [ ] Michele: `/team/michele`
- [ ] Sarah: `/team/dr-sarah-johnson`
- [ ] Alex: `/team/dr-alex-chen`
- [ ] Jessica: `/team/jessica-martinez`
- [ ] Each profile should show:
  - Full bio
  - Credentials badges
  - Specialties list
  - Services they offer (only their services)
  - Statistics (years experience, treatments completed)
  - Contact information
  - "Book Appointment" CTA

---

### 2. Customer Booking Flow

**Test with customer account (e.g., john.smith@example.com)**

#### Step 1: Service Selection
- [ ] Navigate to services page
- [ ] Select a service (e.g., "Botox - Glabella (Frown Lines)")
- [ ] Click "Book Now"

#### Step 2: Provider Selection
- [ ] See provider dropdown with all providers who offer this service
- [ ] For Botox: Should see Dr. Sarah Johnson and Michele
- [ ] For IV services: Should see Dr. Alex Chen and Michele
- [ ] Select provider (e.g., Dr. Sarah Johnson)

#### Step 3: Date & Time Selection
- [ ] Calendar shows only available dates
- [ ] Select a date (e.g., next Monday for Sarah)
- [ ] Available time slots load via AJAX
- [ ] **For Sarah:** Only Mon/Wed/Fri should have slots
- [ ] **For Alex:** Only Tue/Thu/Sat should have slots
- [ ] **For Jessica:** Only Mon-Thu should have slots
- [ ] Time slots respect provider's hours:
  - Sarah: 10am-6pm
  - Alex: 9am-5pm
  - Jessica: 11am-7pm

#### Step 4: Confirmation
- [ ] Review booking details
- [ ] Submit appointment
- [ ] See confirmation message
- [ ] Receive confirmation email (if email configured)
- [ ] Appointment appears in "My Appointments"

---

### 3. Front Desk Booking Flow

**Test with front desk account (e.g., emma@abettersolutionwellness.com)**

#### Access Front Desk Booking
- [ ] Login as front desk staff
- [ ] Navigate to `/admin/appointments`
- [ ] Click "Create Appointment" button
- [ ] Should see front desk booking interface

#### Book for Walk-in Customer
- [ ] Select customer from dropdown (e.g., "John Smith")
- [ ] Select service from dropdown
- [ ] **Provider dropdown should show all 4 providers**
- [ ] Select provider (e.g., "Dr. Alex Chen")
- [ ] Select date (e.g., next Tuesday for Alex)
- [ ] Available time slots load via AJAX
- [ ] **Verify only Tue/Thu/Sat slots show for Alex**
- [ ] Select time slot
- [ ] Add appointment notes (optional)
- [ ] Add admin notes (optional - for Michele only)
- [ ] Submit booking
- [ ] Appointment status = "confirmed" (auto-confirmed)
- [ ] Redirected to appointments list
- [ ] New appointment appears in list

#### Filter Appointments
- [ ] Filter by provider (select "Dr. Sarah Johnson")
- [ ] Should only see Sarah's appointments
- [ ] Filter by status (select "pending")
- [ ] Filter by date range
- [ ] Search by customer name
- [ ] Clear filters

---

### 4. Provider Dashboard Testing

**Test each provider account separately:**

#### Dr. Sarah Johnson Dashboard
- [ ] Login as sarah@abettersolutionwellness.com
- [ ] Navigate to `/provider/dashboard`
- [ ] **Statistics Cards:**
  - [ ] "Today" count (appointments today)
  - [ ] "This Week" count (next 7 days)
  - [ ] "Next 30 Days" count
  - [ ] "Pending" count (pending approvals)
  - [ ] "Completed" count (all-time completed)
- [ ] **Today's Schedule:**
  - [ ] Shows only Sarah's appointments for today
  - [ ] Appointments sorted by start time
  - [ ] Shows customer name, service, time
  - [ ] "View Details" link works
- [ ] **Upcoming Appointments:**
  - [ ] Shows next 7 days (excluding today)
  - [ ] Only Sarah's appointments
  - [ ] Sorted by date, then time
- [ ] Navigate to "View All Appointments"
- [ ] Should see `/provider/appointments`
- [ ] **Verify Sarah CANNOT see:**
  - [ ] Michele's appointments
  - [ ] Alex's appointments
  - [ ] Jessica's appointments

#### Dr. Alex Chen Dashboard
- [ ] Login as alex@abettersolutionwellness.com
- [ ] Verify dashboard shows only Alex's appointments
- [ ] Verify statistics are accurate for Alex only
- [ ] Verify cannot see other providers' appointments

#### Jessica Martinez Dashboard
- [ ] Login as jessica@abettersolutionwellness.com
- [ ] Verify dashboard shows only Jessica's appointments
- [ ] Verify cannot see other providers' appointments

#### Michele Dashboard (Admin + Provider)
- [ ] Login as michele@abettersolutionwellness.com
- [ ] Can access `/provider/dashboard` (sees only her appointments as provider)
- [ ] Can access `/admin` (sees all appointments as admin)
- [ ] Admin appointments page shows filter by provider
- [ ] Can filter to see each provider's appointments
- [ ] Can see all providers in dropdown

---

### 5. Provider Availability Testing

#### Different Schedules
- [ ] **Sarah (Mon/Wed/Fri 10am-6pm):**
  - [ ] Book appointment on Monday at 2pm ✅
  - [ ] Try to book Tuesday - should have no slots ❌
  - [ ] Try to book Wednesday at 9am - should not be available ❌
  - [ ] Try to book Wednesday at 7pm - should not be available ❌

- [ ] **Alex (Tue/Thu/Sat 9am-5pm):**
  - [ ] Book Tuesday at 10am ✅
  - [ ] Try Monday - no slots ❌
  - [ ] Try Saturday at 6pm - not available ❌

- [ ] **Jessica (Mon-Thu 11am-7pm):**
  - [ ] Book Monday at 6pm ✅
  - [ ] Try Monday at 10am - not available ❌
  - [ ] Try Friday - no slots ❌
  - [ ] Try Thursday at 8pm - not available ❌

---

### 6. Authorization & Middleware Testing

#### Customer Access Restrictions
- [ ] Login as customer
- [ ] Try to access `/admin` - should redirect ❌
- [ ] Try to access `/admin/providers` - should redirect ❌
- [ ] Try to access `/provider/dashboard` - should redirect ❌

#### Front Desk Access Restrictions
- [ ] Login as front desk (Emma)
- [ ] Can access `/admin/appointments` ✅
- [ ] Try to access `/admin/providers` - should redirect ❌
- [ ] Try to access `/admin/services` - should redirect ❌
- [ ] Try to access `/provider/dashboard` - should redirect ❌

#### Provider Access Restrictions
- [ ] Login as provider (Sarah)
- [ ] Can access `/provider/dashboard` ✅
- [ ] Can access `/provider/appointments` ✅
- [ ] Try to access `/admin/providers` - should redirect ❌
- [ ] Can view appointment details (only their own)
- [ ] **Cannot see other providers' appointments** ❌

#### Admin Access (Michele)
- [ ] Login as Michele
- [ ] Can access `/admin` ✅
- [ ] Can access `/admin/providers` ✅
- [ ] Can access `/admin/services` ✅
- [ ] Can access `/admin/appointments` ✅
- [ ] Can access `/provider/dashboard` ✅ (dual role)
- [ ] Can see ALL providers' appointments ✅
- [ ] Can filter by any provider ✅

---

### 7. Database Integrity Testing

#### Verify Seeded Data
Run in `php artisan tinker`:

```php
// Check provider count
App\Models\Provider::count(); // Should be 4

// Check each provider's services
$sarah = App\Models\Provider::where('slug', 'dr-sarah-johnson')->first();
$sarah->services()->count(); // Should be 4

$alex = App\Models\Provider::where('slug', 'dr-alex-chen')->first();
$alex->services()->count(); // Should be 3

$jessica = App\Models\Provider::where('slug', 'jessica-martinez')->first();
$jessica->services()->count(); // Should be 2

// Check appointments distribution
App\Models\Appointment::count(); // Should be 13 total
$sarah->appointments()->count(); // Should have some
$alex->appointments()->count(); // Should have some
$jessica->appointments()->count(); // Should have some

// Check front desk staff
App\Models\Customer::where('role', 'front_desk')->count(); // Should be 2

// Check test customers
App\Models\Customer::where('role', 'customer')->count(); // Should be 5
```

---

### 8. AJAX Functionality Testing

#### Time Slot Loading
- [ ] Open browser dev tools (Network tab)
- [ ] Start booking appointment
- [ ] Select provider and date
- [ ] **Verify AJAX request:**
  - [ ] Request to `/api/availability/slots`
  - [ ] Query params: `service_id`, `provider_id`, `date`
  - [ ] Response contains `slots` array
  - [ ] Time slots render without page reload
- [ ] Change provider - slots update immediately
- [ ] Change date - slots update immediately

---

### 9. SEO & Meta Tags Testing

- [ ] Visit `/team` - check page title and meta description
- [ ] Visit `/team/michele` - check Michele's meta tags
- [ ] Visit `/team/dr-sarah-johnson` - check Sarah's meta tags
- [ ] View page source, verify meta tags present

---

## Common Issues & Troubleshooting

### Issue: "Provider not found"
- **Solution:** Ensure you're logged in with correct credentials
- **Check:** Run `php artisan db:seed --class=TestDataSeeder` if data is missing

### Issue: "No time slots available"
- **Check:** Provider's availability for selected day of week
- **Example:** Sarah works Mon/Wed/Fri only, not Tue/Thu
- **Check:** Time range (Sarah: 10am-6pm, Alex: 9am-5pm, Jessica: 11am-7pm)

### Issue: "Unauthorized" or redirect
- **Check:** User role matches required permission
- **Front desk:** Cannot access `/admin/providers`
- **Provider:** Cannot access `/admin/*` (except appointments)
- **Customer:** Cannot access `/admin/*` or `/provider/*`

### Issue: Can see other providers' appointments
- **Expected:** Each provider should ONLY see their own
- **Check:** `/provider/dashboard` and `/provider/appointments`
- **Admin exception:** Michele can see all (admin role)

---

## Success Criteria

✅ **All tests passing means:**
1. All 4 providers can login and see only their appointments
2. Front desk can book for any provider
3. Customers can select any provider and see correct availability
4. Each provider's schedule is respected (days and hours)
5. Authorization works correctly (roles cannot access unauthorized routes)
6. Public team pages show all providers
7. AJAX slot loading works for all providers
8. No provider can see another provider's appointments
9. Michele (admin) can see everything

---

## Quick Test Script

Copy and run this in your browser console on the booking page to verify AJAX:

```javascript
// Test time slot API
fetch('/api/availability/slots?service_id=1&provider_id=2&date=2025-12-16')
  .then(r => r.json())
  .then(data => console.log('Slots:', data.slots));
```

Expected response:
```json
{
  "slots": [
    {"time": "10:00", "formatted": "10:00 AM"},
    {"time": "10:30", "formatted": "10:30 AM"},
    ...
  ],
  "provider": {...},
  "date": "2025-12-16"
}
```

---

## Next Steps After Testing

1. **If all tests pass:** System is ready for production!
2. **If issues found:** Document issues and fix before deployment
3. **Customization:** Add real provider photos, update bios, adjust schedules
4. **Email Setup:** Configure email notifications for bookings
5. **Payment:** Integrate Stripe/Square for payments
6. **Reminders:** Add SMS/email appointment reminders

---

**Testing completed on:** _________________
**Tested by:** _________________
**Issues found:** _________________
**All tests passed:** ☐ Yes ☐ No
