# AGENTS.md

## Project Overview

PROPFlow is a Property Sales CRM & Inventory Management System built with Laravel.

The system is designed for property developers, property agencies, sales teams, managers, finance teams, and administrators.

The application manages:

- Property projects
- Clusters
- Property units
- Leads
- Customers
- Sales agents
- Follow-ups
- Site visits
- Negotiations
- Bookings
- Transactions
- Mortgage / KPR
- Payments
- Commissions
- Documents
- Notifications
- Reports
- Analytics

This project must be developed as a real-world production application, not as a simple CRUD demo.

---

# Core Engineering Principles

All development must prioritize:

1. Correctness
2. Security
3. Data integrity
4. Maintainability
5. DRY
6. KISS
7. SOLID
8. Performance
9. Scalability
10. Readability

Do not sacrifice readability for unnecessary abstraction.

Do not over-engineer simple problems.

Always prefer Laravel conventions before creating custom architecture.

---

# Technology Stack

Use:

- Laravel
- PHP latest compatible version
- MySQL / MariaDB
- Blade
- Livewire where appropriate
- Tailwind CSS
- Font Awesome
- SweetAlert2
- Vite
- Laravel Queue
- Laravel Scheduler
- Laravel Notification
- UUID for main entities

Do not introduce React, Vue, or another SPA framework unless explicitly requested.

---

# Architecture Rules

Use Laravel conventions first.

Recommended structure:

```text
app/
├── Actions/
├── DTOs/
├── Enums/
├── Exceptions/
├── Helpers/
├── Http/
│   ├── Controllers/
│   └── Requests/
├── Jobs/
├── Livewire/
├── Models/
├── Notifications/
├── Policies/
├── Services/
└── Support/
```

Only create folders that are actually needed.

Do not create empty architectural layers just for appearance.

---

# Controller Rules

Controllers must remain thin.

Controllers may:

- Receive HTTP requests
- Call Form Requests
- Call Services or Actions
- Return views or responses

Controllers must not contain complex business logic.

Bad:

```php
public function store(Request $request)
{
    // 100+ lines:
    // validation
    // lead creation
    // sales assignment
    // notification
    // scoring
    // audit log
}
```

Preferred:

```php
public function store(StoreLeadRequest $request, LeadService $leadService)
{
    $lead = $leadService->create($request->validated());

    return redirect()
        ->route('crm.leads.show', $lead)
        ->with('success', 'Lead berhasil ditambahkan.');
}
```

---

# Service Layer

Business logic belongs in Services or Actions.

Examples:

```text
LeadService
LeadAssignmentService
LeadScoringService
PropertyService
PropertyUnitService
BookingService
TransactionService
PaymentService
MortgageService
CommissionService
FollowUpService
SiteVisitService
NotificationService
ReportingService
DashboardService
```

Do not create a God Service.

If a service becomes too large, split it by business capability.

Example:

Bad:

```text
SalesService
- create lead
- calculate commission
- process payment
- approve mortgage
- generate report
```

Preferred:

```text
LeadService
CommissionService
PaymentService
MortgageService
ReportingService
```

---

# Actions

Use Action classes when one specific business operation deserves its own class.

Examples:

```text
AssignLeadToSales
ConvertLeadToCustomer
ApproveBooking
CancelBooking
VerifyPayment
ApproveCommission
```

Do not create Action classes for trivial one-line operations.

---

# DRY Principle

Avoid duplicated knowledge and duplicated business rules.

Do not blindly deduplicate code only because two code blocks look similar.

Business rules from different domains may legitimately be separate.

Reuse through:

- Service
- Action
- Blade Component
- Query Scope
- Helper
- Trait
- Utility
- Enum

only when reuse is meaningful.

---

# KISS Principle

Prefer the simplest correct implementation.

Do not introduce:

- Repository Pattern
- Interface
- Factory
- Strategy Pattern
- Event Sourcing
- CQRS

unless the problem genuinely benefits from it.

Patterns must solve problems, not create complexity.

---

# SOLID

Apply SOLID pragmatically.

## Single Responsibility

One class should have one clear reason to change.

## Open / Closed

Use extensible design for features likely to gain new implementations.

Example:

Lead assignment strategies:

```text
RoundRobinLeadAssignment
WorkloadLeadAssignment
ProjectLeadAssignment
ManualLeadAssignment
```

## Liskov Substitution

Implementations of an interface must behave consistently with the expected contract.

## Interface Segregation

Do not create large interfaces containing unrelated methods.

## Dependency Inversion

Depend on abstractions when multiple implementations or infrastructure independence provides real value.

Do not create interfaces for every service automatically.

---

# Model Rules

Models should primarily contain:

- relationships
- casts
- scopes
- accessors
- mutators
- simple domain helpers

Models must not become God Models.

Avoid putting full workflow logic inside Eloquent models.

Use `$fillable`.

Do not use:

```php
protected $guarded = [];
```

unless there is a strong and reviewed reason.

---

# UUID

Use UUID for main business entities.

Examples:

```text
users
companies
projects
clusters
property_units
leads
customers
bookings
transactions
payments
mortgages
commissions
```

Use appropriate Laravel UUID helpers.

Do not manually generate weak random identifiers.

---

# Database Rules

Always use:

- Foreign keys
- Appropriate indexes
- Unique constraints
- Correct nullable definitions
- Proper data types

Do not use `string` for everything.

Example:

Use:

```php
$table->decimal('price', 15, 2);
```

instead of:

```php
$table->string('price');
```

Add indexes to fields commonly used for:

- WHERE
- JOIN
- ORDER BY
- filtering
- foreign keys
- status

---

# Database Transactions

Use database transactions for operations that must succeed or fail together.

Examples:

- Booking creation
- Booking approval
- Lead conversion
- Property sale
- Payment verification
- Mortgage approval
- Commission generation

Example:

```php
DB::transaction(function () {
    //
});
```

---

# Concurrency

Prevent race conditions.

Property units must never be booked by two customers at the same time.

Use database locking where appropriate.

Example:

```php
PropertyUnit::query()
    ->whereKey($unitId)
    ->lockForUpdate()
    ->firstOrFail();
```

Always re-check availability inside the transaction.

Do not trust UI state as the source of truth.

---

# Enum Rules

Use PHP Enum for controlled states.

Examples:

```text
LeadStatus
LeadTemperature
PropertyUnitStatus
BookingStatus
PaymentStatus
MortgageStatus
CommissionStatus
SiteVisitStatus
```

Avoid magic strings such as:

```php
if ($lead->status === 'hot')
```

Prefer:

```php
if ($lead->temperature === LeadTemperature::HOT)
```

---

# Form Request

Validation belongs in Form Request classes.

Examples:

```text
StoreLeadRequest
UpdateLeadRequest
StorePropertyRequest
UpdatePropertyRequest
StoreBookingRequest
ApproveBookingRequest
RejectBookingRequest
StorePaymentRequest
VerifyPaymentRequest
StoreSiteVisitRequest
```

Do not place large validation arrays in controllers.

---

# Authorization

Authorization must be enforced on the backend.

Use:

- Policies
- Gates
- Middleware

Never rely only on hidden buttons.

Bad:

```blade
@if(auth()->user()->isAdmin())
    <button>Delete</button>
@endif
```

without backend authorization.

Every protected action must validate permissions server-side.

---

# Roles

Expected roles include:

```text
Super Admin
Company Owner
Sales Manager
Team Leader
Sales / Agent
Finance
Property Admin
```

Each role must receive only the permissions required for its responsibility.

Follow least-privilege principles.

---

# Multi-Tenant Awareness

The application should remain multi-company ready.

Main business data should support company isolation.

Examples:

```text
company_id
```

for:

- projects
- leads
- customers
- bookings
- payments
- commissions
- users

Never trust a `company_id` sent directly from the frontend.

Determine tenant context from the authenticated user/session.

Never allow cross-company data leakage.

---

# Blade Rules

Blade must contain presentation logic only.

Do not put business logic in Blade.

Forbidden:

```blade
@php
    // large business calculation
@endphp
```

Do not perform database queries inside Blade.

Forbidden:

```blade
{{ \App\Models\Lead::count() }}
```

Prepare data in the appropriate backend layer.

---

# JavaScript Rules

No custom inline JavaScript inside Blade.

Forbidden:

```html
<script>
    // custom logic
</script>
```

Custom JavaScript must live under:

```text
resources/js/
```

Recommended:

```text
resources/js/
├── app.js
└── modules/
    ├── sweetalert.js
    ├── lead.js
    ├── booking.js
    ├── pipeline.js
    └── siteplan.js
```

Avoid global variables.

Prefer reusable modules.

---

# CSS Rules

No custom inline CSS in Blade.

Forbidden:

```html
<style>
    ...
</style>
```

and avoid:

```html
<div style="..."></div>
```

unless there is a rare technical reason.

Custom styles belong under:

```text
resources/css/
```

---

# SweetAlert2 Rules

SweetAlert2 must be used consistently for important interaction states.

Use SweetAlert2 for:

- success
- error
- warning
- delete confirmation
- logout confirmation
- booking approval
- booking rejection
- cancellation
- payment verification
- commission approval
- lead reassignment

Do not duplicate SweetAlert configuration everywhere.

Create reusable utilities.

Example conceptual API:

```javascript
confirmAction();
confirmDelete();
confirmLogout();
showSuccess();
showError();
showWarning();
```

---

# Logout

Logout must require confirmation through SweetAlert2.

Example message:

```text
Keluar dari akun?

Anda harus login kembali untuk mengakses sistem.

[Batal] [Ya, Keluar]
```

Do not immediately logout after clicking the logout menu.

---

# Font Awesome

Use Font Awesome consistently for application icons.

Do not use emoji as primary UI icons.

Use icons for:

- Dashboard
- Leads
- Customers
- Property
- Booking
- Payment
- Mortgage
- Commission
- Reports
- Users
- Settings
- Logout

Do not mix multiple icon libraries unless explicitly needed.

---

# UI Design Rules

Design direction:

```text
Luxury
Premium
Modern
Corporate
Minimal
Elegant
```

The application should look like a high-end property SaaS platform.

It must not look like a generic bootstrap admin template.

---

# No Gradient

Gradient is strictly prohibited.

Do not use gradient in:

- Sidebar
- Header
- Button
- Card
- Hero
- Badge
- Login page
- Chart
- Modal

Use solid colors only.

Recommended palette:

```text
Background: #F7F6F2
Surface: #FFFFFF
Dark: #161616
Dark Secondary: #262626
Gold: #B89B5E
Soft Gold: #D7C49E
Border: #E8E4DA
Muted Text: #79766F
```

Gold must be used as an accent, not as the dominant color.

---

# Component Rules

Use reusable Blade components when they reduce duplication.

Examples:

```text
x-button
x-input
x-select
x-card
x-badge
x-modal
x-page-header
x-empty-state
x-stat-card
```

Do not turn every HTML tag into a component.

Components should provide meaningful reuse.

---

# Table Rules

Tables should support where appropriate:

- Search
- Filter
- Sorting
- Pagination
- Status badge
- Action dropdown

Avoid displaying many action buttons directly.

Preferred:

```text
...
View
Edit
Assign
Delete
```

inside an action menu.

---

# Form Rules

All forms must:

- Have visible labels
- Show validation errors
- Preserve old input where appropriate
- Use appropriate input types
- Provide helper text when needed
- Remain responsive

Do not rely on placeholder text as the only label.

---

# Loading States

For Livewire or asynchronous actions:

- Show loading state
- Disable action button while processing
- Prevent duplicate submission

Examples:

```text
Booking...
Saving...
Verifying...
```

---

# Business State Rules

State transitions must be validated.

Example Property Unit lifecycle:

```text
AVAILABLE
↓
RESERVED
↓
BOOKED
↓
SOLD
```

Do not allow arbitrary status changes.

Example:

```text
SOLD → AVAILABLE
```

must not happen without an approved cancellation/reversal workflow.

---

# Lead Rules

Lead lifecycle may include:

```text
NEW
CONTACTED
QUALIFIED
SITE_VISIT
NEGOTIATION
BOOKING
WON
LOST
```

When a lead is marked LOST, require a reason.

Examples:

- Price too high
- Location
- Competitor
- KPR rejected
- No response
- Postponed
- Not interested
- Other

---

# Lead Scoring

Lead scoring logic must not be implemented inside controllers or Blade.

Place scoring logic in:

```text
LeadScoringService
```

Make scoring configurable where practical.

Example:

```text
Budget match             +15
Purchase < 30 days       +20
Site visit               +20
Asked for KPR simulation +15
Responsive lead          +10
```

---

# Lead Assignment

Lead assignment must be extensible.

Possible strategies:

```text
Round Robin
By Project
By Team
By Workload
By Location
Manual
```

Use Strategy Pattern only if it genuinely improves maintainability.

---

# Booking Rules

Booking is a critical domain action.

Before creating a booking:

1. Validate user authorization
2. Validate unit exists
3. Validate unit belongs to the correct company
4. Lock property unit row
5. Re-check unit availability
6. Create booking
7. Update unit status
8. Record activity
9. Record audit log
10. Send notification if needed

All critical database changes must happen inside one transaction.

---

# Payment Rules

Payment verification must be handled by authorized finance users.

Payment proof must not be considered valid merely because it was uploaded.

Maintain:

- amount
- payment date
- payment method
- reference
- proof
- verifier
- status

---

# Commission Rules

Commission calculation belongs in:

```text
CommissionService
```

Do not calculate commissions inside Blade.

Do not duplicate commission formulas across controllers.

Keep commission rules configurable where feasible.

---

# Sensitive Documents

Customer documents must use private storage.

Examples:

- KTP
- KK
- NPWP
- Salary slip
- Bank statement
- KPR document

Do not store sensitive documents in:

```text
public/
```

Use authorization before file access.

Validate:

- MIME type
- extension
- size

Use randomized storage names.

Do not expose physical storage paths.

---

# File Upload Security

Never trust original filenames.

Do not use:

```php
$request->file('document')->getClientOriginalName()
```

as the actual storage filename.

Use generated names such as UUID.

Validate both MIME and allowed file types.

---

# Query Performance

Always watch for N+1 issues.

Use:

```php
with()
load()
withCount()
select()
paginate()
```

appropriately.

Do not eagerly load every relation automatically.

Only load what the current screen needs.

---

# Pagination

Do not load large datasets using:

```php
Model::all()
```

for index screens.

Use pagination.

---

# Caching

Use caching only when it provides meaningful benefit.

Possible candidates:

- Dashboard metrics
- Static master data
- Expensive aggregate queries

Do not cache rapidly changing transactional data blindly.

---

# Queue

Use queue for slow non-blocking processes such as:

- Email notifications
- Report generation
- Import
- Export
- Image processing
- Large document processing

Do not queue actions where the result must be immediately known for data integrity.

---

# Notifications

Prefer Laravel Notification.

Supported channels may include:

- Database
- Email

Notification classes should focus on presentation/delivery.

Business decisions about whether to notify belong outside notification classes.

---

# Audit Log

Important actions must create audit logs.

Examples:

- Lead reassignment
- Price modification
- Booking approval
- Booking cancellation
- Payment verification
- Commission approval
- Unit status change
- Sensitive document access

Record where appropriate:

```text
user
action
entity
entity_id
before
after
ip
user_agent
timestamp
```

Never store:

- Password
- Access token
- Secret
- OTP
- Full sensitive credentials

---

# Security

Always consider:

- Authentication
- Authorization
- Broken access control
- IDOR / BOLA
- CSRF
- XSS
- SQL injection
- Mass assignment
- File upload vulnerabilities
- Rate limiting
- Session security
- Sensitive data exposure
- Logging

Do not disable Laravel security features without strong justification.

---

# Query Safety

Prefer Eloquent and Query Builder.

Do not build raw SQL by concatenating user input.

Forbidden:

```php
DB::select("SELECT * FROM leads WHERE name = '$name'");
```

Use parameter binding.

---

# Route Naming

Use named routes consistently.

Examples:

```text
dashboard
crm.leads.index
crm.leads.show
crm.leads.store

properties.projects.index
properties.units.index

sales.bookings.index

finance.payments.index
finance.payments.verify

reports.sales.index
```

Do not hardcode application URLs in Blade.

Use:

```php
route()
```

---

# Naming Convention

Use descriptive names.

Bad:

```php
$data
$res
$tmp
$obj
$x
```

Preferred:

```php
$lead
$customer
$propertyUnit
$booking
$payment
$commissionAmount
$assignedSales
```

Method names should describe intent.

Bad:

```php
handleData()
process()
doAction()
```

Preferred:

```php
assignLeadToSales()
approveBooking()
verifyPayment()
calculateCommission()
convertLeadToCustomer()
```

---

# Comments

Do not write comments explaining obvious syntax.

Bad:

```php
// Get user
$user = auth()->user();
```

Comments should explain:

- non-obvious business rules
- technical constraints
- intentional trade-offs
- unusual implementation decisions

Prefer self-documenting code.

---

# Error Handling

Use meaningful domain exceptions where appropriate.

Examples:

```text
PropertyUnitUnavailableException
LeadAlreadyConvertedException
InvalidBookingStateException
InsufficientDiscountPermissionException
PaymentAlreadyVerifiedException
```

Do not expose raw internal exceptions to end users.

Show a clear user-friendly message.

---

# Testing

Create tests for critical flows.

Priority tests:

- Authentication
- Authorization
- Tenant isolation
- Lead creation
- Lead assignment
- Lead conversion
- Booking
- Duplicate booking prevention
- Booking approval
- Payment verification
- Commission calculation
- Document authorization
- Property unit state transition

Prefer Feature Tests for real business flows.

Use Unit Tests for isolated domain logic where valuable.

---

# Seeder Rules

Seed realistic demo data.

Do not use meaningless values such as:

```text
Test 1
Test 2
Lorem Ipsum Company
```

Use realistic:

- Property project names
- Cluster names
- Leads
- Customers
- Sales agents
- Property units
- Bookings
- Payments

Seed data must allow the dashboard and reports to look realistic.

---

# Migration Rules

Never edit an old migration after it may have been deployed to a shared environment.

Create a new migration for schema changes.

Ensure rollback behavior is reasonable.

Foreign key names and table names must remain consistent.

---

# Development Workflow

Before implementing a feature:

1. Understand the business requirement
2. Identify affected roles
3. Identify authorization rules
4. Define data model
5. Define state transitions
6. Identify transaction boundaries
7. Identify concurrency risks
8. Design service/action layer
9. Implement validation
10. Implement backend logic
11. Implement UI
12. Add SweetAlert interaction
13. Add tests
14. Review security
15. Review query performance
16. Review DRY, KISS, SOLID

Do not start from Blade first.

---

# Before Finishing Any Feature

Check all of the following:

- Is there business logic inside Blade?
- Is there too much logic inside Controller?
- Is there duplicated business logic?
- Is the abstraction justified?
- Are there magic strings?
- Are there N+1 queries?
- Is authorization enforced server-side?
- Is the request validated?
- Does the operation need a DB transaction?
- Is there a concurrency risk?
- Is tenant isolation protected?
- Are sensitive files private?
- Is JavaScript separated from Blade?
- Is custom CSS separated from Blade?
- Is SweetAlert2 implemented consistently?
- Are Font Awesome icons used consistently?
- Is there any gradient?
- Are error states handled?
- Is there a loading state?
- Are tests needed?

Refactor before considering a feature complete if critical issues remain.

---

# Prohibited Practices

Do not:

- Put complex business logic in Controller
- Put business logic in Blade
- Query the database from Blade
- Use inline custom JavaScript in Blade
- Use inline custom CSS in Blade
- Use gradient
- Use emoji as primary application icons
- Trust frontend authorization
- Trust frontend status values
- Trust company IDs sent by user
- Store sensitive documents publicly
- Use raw SQL concatenated with user input
- Create God Controllers
- Create God Services
- Create God Models
- Overuse Repository Pattern
- Create interfaces without purpose
- Add design patterns merely for appearance
- Use `Model::all()` for large lists
- Ignore race conditions
- Ignore transaction boundaries
- Ignore multi-tenant isolation
- Ignore N+1 queries

---

# Preferred Engineering Style

Prefer:

```text
Simple over clever
Explicit over magical
Readable over short
Secure over convenient
Consistent over experimental
Laravel conventions over custom patterns
Business correctness over visual shortcuts
```

---

# Agent Behavior

When acting as an AI coding agent:

- Inspect existing code before creating new architecture
- Reuse existing conventions where they are good
- Do not rewrite unrelated code
- Do not perform large refactors unless required
- Keep changes scoped to the requested feature
- Preserve backward compatibility where practical
- Do not delete existing behavior without understanding why it exists
- Mention architectural problems discovered during implementation
- Fix obvious security issues related to the current task
- Avoid changing unrelated styling
- Avoid adding dependencies unless necessary
- Prefer built-in Laravel features before third-party packages

If uncertain between a complicated custom solution and a Laravel-native solution, prefer Laravel-native.

---

# Final Standard

Every implemented feature should feel like it belongs in a production-grade luxury Property CRM.

The result must not feel like:

```text
Generic CRUD Admin Panel
```

It should feel like:

```text
Luxury Enterprise Property Sales CRM
```

while keeping the internal architecture clean, secure, simple, and maintainable.
