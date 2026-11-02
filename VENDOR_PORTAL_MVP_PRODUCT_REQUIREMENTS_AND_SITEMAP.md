# Vendor Portal — MVP Product Requirements & Sitemap

**Document type:** Product brief, sitemap, workflow specification, and implementation handoff  
**Audience:** Product designer, UX designer, frontend/backend coding agent, QA  
**Product scope:** Vendor web application only  
**Status:** MVP definition based on the product workflow described by the founder  
**Last updated:** 2 November 2026

---

## 1. Product in one paragraph

This platform connects customers who need goods transported from one location to another with suitable truck drivers. The customer submits a transport request through the customer mobile app. The vendor is the operations intermediary: the vendor reviews the request, finds nearby approved and available drivers using their latest shared locations and vehicle information, selects a group of suitable drivers, and sends them the request. Drivers respond **Accept** or **Reject** in the driver mobile app. The vendor sees each response and contacts drivers who accept to discuss the operational details and decide the next step. The vendor portal is the web-based workspace used to run this process.

The platform has three product surfaces overall:

1. Customer mobile application — creates transport requests.
2. Driver mobile application — registers drivers, shares availability/location after approval, and receives/responds to requests.
3. Vendor web application — manages requests, drivers, matching, responses, and onboarding review.

**This document covers only the Vendor web application.** The Admin role and Admin portal are not part of this MVP.

## 2. Product boundaries and terminology

### 2.1 What “Vendor” means in this product

The vendor is the central operations team or intermediary that coordinates between customers and independent driver partners. It is not necessarily the owner of the trucks. The vendor account is the authenticated user of this web portal.

### 2.2 Terminology used in the interface

Use consistent terms throughout the product:

| Term | Meaning |
|---|---|
| Transport Request / Request | A customer's submitted requirement to move goods from pickup to destination. |
| Driver Partner | A registered driver with a vehicle. Use respectful “Driver Partner” language in the product. |
| Driver Invitation | A request sent by the vendor to a selected driver asking whether they can take the load. |
| Driver Response | The driver's Accept or Reject response to an invitation. |
| Matching | Finding suitable nearby approved drivers for a transport request. |
| Vendor Follow-up | The vendor's call or direct communication with a driver after acceptance. |
| Approved Driver | A driver whose identity and vehicle documents have passed vendor review. |
| Available Driver | An approved driver who is currently sharing a sufficiently fresh location and is marked available to receive requests. |

Avoid using “booking”, “load”, “shipment”, “query”, and “request” interchangeably in interface labels. The recommended primary object is **Transport Request**. Use “Load details” for the goods being transported and “Driver Requests” for invitations sent to drivers.

## 3. MVP goals

The vendor must be able to:

1. Sign in securely to the vendor portal.
2. See new customer transport requests and understand what needs attention.
3. Open a request and inspect route, cargo, requirements, photos, and relevant customer contact information.
4. Find nearby approved drivers around the pickup point.
5. Filter matching drivers by distance, truck/vehicle type, capacity, and availability.
6. Choose how many nearby drivers to consider and send invitations to selected drivers.
7. See which invited drivers have accepted, rejected, or not yet responded.
8. Contact drivers who accept and record the follow-up outcome.
9. Review new driver-partner applications and their KYC/vehicle documents, then approve, reject, or request corrections.
10. Find a driver later and inspect their vehicle, verification, availability, and contact information.

### MVP success criteria

- A newly submitted customer request appears in the vendor portal without manual re-entry.
- A vendor can move from a new request to sending selected driver invitations without losing request details.
- Driver invitations are linked to the originating request and show an individual response status.
- Driver location age/freshness is visible, so vendors do not mistake stale coordinates for a live location.
- A vendor can inspect and make a review decision on a driver application.
- The interface has clear empty, loading, error, and permission states; no dead buttons or fake functionality.

## 4. Explicitly out of scope for this MVP

Do not build these unless the founder explicitly adds them to scope:

- Admin portal, Admin login, vendor account management, or global platform configuration.
- Multi-vendor organisation management or vendor-to-vendor permissions.
- Automated bidding, auctions, quote comparison, or driver rate submission.
- Commission calculation, vendor margins, payments, wallet, payouts, invoices, or accounting.
- Customer checkout or online payment.
- Full trip execution workflow, turn-by-turn navigation, proof of delivery, POD uploads, or delivery settlement.
- Automated customer-driver chat or masked calling; vendor follow-up can initially be handled by normal phone calls.
- Advanced BI, custom reports, predictive analytics, or complex performance scorecards.
- Fleet maintenance, fuel, expense, route optimisation, or driver payroll modules.
- Complex SLA automation or multi-stage approval chains.

The portal can preserve the data model so these can be added later, but do not invent screens for them in the MVP.

---

## 5. Core business workflow

### 5.1 Customer request to driver invitation

```text
Customer submits transport request in customer app
                    |
                    v
Vendor portal receives a new request
                    |
                    v
Vendor opens request and reviews cargo + route details
                    |
                    v
Vendor opens Find Drivers / Match Drivers
                    |
                    v
System finds approved, available drivers near pickup
                    |
                    v
Vendor filters by radius, driver count, vehicle type, capacity
                    |
                    v
Vendor selects one or more drivers and sends invitations
                    |
                    v
Driver app receives invitation (without customer identity/contact)
                    |
             +------+------+
             |             |
          Accept          Reject
             |             |
             v             v
Vendor sees Accepted   Vendor sees Rejected
             |
             v
Vendor contacts the driver directly
             |
             v
Vendor records follow-up outcome / next operational status
```

### 5.2 Driver onboarding and approval

```text
Driver completes mobile onboarding in driver app
                    |
                    v
Driver submits personal, KYC, vehicle and document details
                    |
                    v
Vendor sees a pending driver application
                    |
                    v
Vendor reviews the identity, licence, vehicle and documents
                    |
          +---------+----------+
          |         |          |
        Approve   Needs info  Reject
          |         |          |
          v         v          v
Driver can    Driver updates   Application rejected
be activated  requested items
and share
availability
```

### 5.3 Important privacy rule

A driver invitation should contain enough information for the driver to decide whether the job is suitable: pickup, destination, goods category/description, approximate quantity/weight, vehicle/capacity needs, and relevant handling requirements. **Do not expose the customer's name, phone number, email, or other direct identity/contact information in the initial driver invitation.** The vendor retains access to customer contact information for coordination. Share further customer details only when the operational process requires it and the business has authorised it.

### 5.4 Location rule

The driver app is responsible for collecting and sharing location. The vendor portal must not assume that every driver's location is live. Each location record should include an update timestamp and a freshness status. Only approved drivers who have enabled availability and have a recent, usable location should appear as currently matchable. Show “Last updated … ago” or a stale/offline indicator. Do not present stale coordinates as real-time.

---

## 6. Vendor portal sitemap

### 6.1 Recommended primary navigation

Keep the navigation deliberately small. Recommended desktop sidebar:

1. **Overview**
2. **Transport Requests**
3. **Drivers**
4. **Driver Applications**
5. **Activity**

Bottom utility area:

- Help & Support
- My Account
- Sign out

If the product needs to be even leaner for the first release, Activity can be a view inside individual request details rather than a separate top-level page. The first four primary destinations are essential to the defined workflow.

### 6.2 Sitemap tree

```text
Vendor Portal
│
├── Authentication
│   ├── Vendor Login
│   └── Forgot Password / Reset Password
│
├── Overview
│   ├── KPI summary
│   ├── Requests needing attention
│   ├── Recent request activity
│   └── Driver availability snapshot (small map/list summary)
│
├── Transport Requests
│   ├── Request List
│   │   ├── New
│   │   ├── Matching / Invitations Sent
│   │   ├── Awaiting Driver Response
│   │   ├── Accepted / Follow-up Needed
│   │   ├── In Progress / Confirmed (manual status for MVP)
│   │   └── Closed / Cancelled
│   └── Request Details
│       ├── Route and customer request summary
│       ├── Goods and handling details
│       ├── Customer contact panel (vendor-only)
│       ├── Driver invitation / response list
│       ├── Activity timeline
│       └── Find Drivers
│           ├── Map + nearby driver list
│           ├── Matching filters
│           ├── Driver quick view
│           ├── Multi-select drivers
│           └── Send Invitations confirmation
│
├── Drivers
│   ├── Driver Directory
│   └── Driver Details
│       ├── Driver profile and contact
│       ├── Vehicle information and photos
│       ├── KYC / document status
│       ├── Availability and last location update
│       └── Driver request history
│
├── Driver Applications
│   ├── Pending Applications
│   └── Application Review
│       ├── Personal information
│       ├── Driving licence and identity proof
│       ├── Vehicle details and side-view photo
│       ├── RC, insurance, fitness, PUC, permits
│       └── Approve / Request Correction / Reject
│
├── Activity (optional standalone section)
│   └── Recent status and invitation events
│
└── Account & Support
    ├── My Account
    ├── Change Password
    └── Help / Contact Support
```

---

## 7. Page-by-page MVP requirements

## 7.1 Vendor Login

**Purpose:** Give authorised vendor users access to the operations portal.

**Required UI:**
- Product logo/name.
- Email or vendor username field (choose based on the existing authentication backend).
- Password field with show/hide control.
- Sign in button.
- Forgot password link.
- Clear invalid-credentials and network-error states.

**Behaviour:**
- Protect all portal routes behind authentication.
- Redirect authenticated vendor users to Overview.
- No Admin login or role selector is needed in this MVP.
- Do not implement public vendor self-registration unless the business specifically requires it.

## 7.2 Overview Dashboard

**Purpose:** Give the vendor an immediate operational picture and direct access to the most urgent work.

### Recommended content order

**Top bar**
- Page title: “Overview”.
- Current date or a concise welcome line (not a large greeting hero).
- Vendor account menu and notification indicator.

**KPI row — keep to four useful metrics**
1. New Requests
2. Awaiting Driver Responses
3. Accepted — Follow-up Needed
4. Available Drivers Now

Optionally show Pending Driver Applications as a small attention badge or fifth metric only if document approval is a regular daily task.

**Requests needing attention**
- Compact table/list of latest requests requiring a vendor action.
- Request ID, pickup → destination, goods category, received time, current status, and primary action “Open”.
- Prioritise New and Accepted — Follow-up Needed.

**Driver availability snapshot**
- A small map or compact panel showing the number of approved/available drivers and a “Find Drivers” entry point.
- Do not build a large analytics map on the dashboard; the full matching map belongs in Request Details → Find Drivers.

**Recent activity**
- A short chronological list: request received, invitations sent, driver accepted/rejected, application submitted, application approved.

### Do not put on Overview

No revenue charts, commission totals, payout charts, customer lifetime value, driver earnings, complex graphs, or decorative KPI cards without an operational decision attached to them.

## 7.3 Transport Requests — List

**Purpose:** Let the vendor process every customer request from intake to driver follow-up.

### Table columns

| Column | Content |
|---|---|
| Request ID | Unique request reference |
| Route | Pickup city/area → destination city/area |
| Goods | Category and short description |
| Load | Quantity and approximate weight |
| Received | Date/time submitted |
| Driver responses | Accepted / invited count, e.g. 2 of 5 accepted |
| Status | Current request status |
| Action | View details |

### Filters
- Status.
- Date range.
- Search by Request ID, pickup or destination.
- Optional goods category.

### List behaviour
- Default sort: newest requests first, with action-required requests clearly surfaced.
- Use pagination or a practical “Load more” pattern.
- Preserve filters when returning from a request detail page.
- Use visible, text-based status labels, not colour alone.

## 7.4 Transport Request — Details

**Purpose:** Give the vendor all information needed to assess the job and find suitable drivers.

### Section A — Request header
- Request ID.
- Status.
- Submitted date/time.
- Primary action based on state: “Find Drivers”, “View Responses”, or “Continue Follow-up”.

### Section B — Route
- Pickup address and map pin.
- Destination address and map pin.
- Map/route preview where map integration exists.
- Do not fabricate mileage or ETA if routing data is not available.

### Section C — Goods details
- Goods category.
- Description.
- Item count / quantity.
- Approximate weight.
- Customer photos with preview.
- Fragile indicator.
- Loading/unloading assistance requirement.
- Special handling instructions.

### Section D — Customer contact (vendor-only)
- Customer name.
- Phone number with Call action.
- Email if supplied.
- Keep this section hidden from the driver invitation payload.

### Section E — Driver invitation results
For each invited driver show:
- Driver name or driver reference.
- Vehicle type/capacity.
- Invitation sent time.
- Response: Pending / Accepted / Rejected / Expired / Failed.
- Accepted responses should be visually prominent.
- Vendor action: “Call Driver” after acceptance.

### Section F — Activity timeline
- Request created.
- Vendor began matching.
- Invitations sent (including number of recipients).
- Each driver response and timestamp.
- Vendor follow-up note/status.

### Vendor follow-up notes
Provide a simple note field and a small outcome selector, such as:
- Contact pending
- Contacted — discussing details
- Proceeding
- Not proceeding

Do not add complex CRM pipelines. These notes are for operational continuity only.

## 7.5 Find Drivers — Matching View

**Purpose:** Find the closest suitable drivers for the current request and send the request to selected drivers.

This is one of the most important screens in the portal. It should be designed as a focused dispatch workspace, not a generic analytics page.

### Recommended desktop layout

- Header: Request ID and a compact pickup → destination summary.
- Filter strip across the top.
- Main area: map and driver results list side by side.
- Sticky selection footer/panel with selected count and “Send Request” action.

### Filters and controls

| Control | Requirement |
|---|---|
| Search radius | Configurable nearby radius, e.g. 5, 10, 15, 20, 50 km, subject to available product configuration. |
| Number of drivers | Desired result/selection limit such as 5, 10, 15, 20, or a custom value. |
| Vehicle type | Filter by available vehicle category. |
| Minimum capacity | Must be compatible with requested goods weight/load requirement. |
| Availability | Default to available drivers only. |
| Sort | Nearest to pickup first by default. |

Do not confuse **search radius** with **number of drivers to invite**. They are separate controls.

### Driver result card
Each driver row/card should show:
- Driver name or unique partner ID.
- Vehicle type/model.
- Vehicle capacity.
- Distance from pickup point.
- Last location update time / freshness indicator.
- Availability indicator.
- Approved/verified state.
- Selection checkbox.

### Map behaviour
- Centre the map on the pickup location.
- Display a distinct pickup marker.
- Display matchable drivers as separate markers.
- Selecting a list item highlights the corresponding map marker and vice versa.
- Show a map/list empty state when no eligible drivers are found.
- Provide a clear way to refresh locations/results.
- Do not show a driver as “Live” if the location timestamp is stale.

### Matching rules
- Only approved drivers can be invited.
- Only drivers currently marked available should appear by default.
- Only drivers with usable, sufficiently recent location data can be distance-ranked.
- Apply vehicle type and capacity filters where the customer request specifies them.
- Sort by distance from pickup by default.
- Show why a driver is excluded only when useful (e.g. insufficient capacity); do not clutter the default list.

### Send invitations
- Vendor selects one or more drivers.
- Button label: “Send Request to N Drivers”.
- Confirmation summary identifies the number of selected drivers and request reference.
- After sending, show success feedback and update the request's invitation list.
- Prevent duplicate active invitations to the same driver for the same request unless the previous invitation has expired/cancelled.

### Driver-facing invitation payload
Send only the relevant job details:
- Request reference.
- Pickup and destination.
- Goods category and description.
- Approximate weight/quantity.
- Required vehicle capacity/type if applicable.
- Fragile, loading/unloading, and special handling flags.

Do not send customer name, phone number, email, or personal profile details at this stage.

## 7.6 Drivers — Directory

**Purpose:** Let the vendor find and inspect drivers who have completed onboarding.

### Table/list columns
- Driver name / partner ID.
- Phone number (vendor-only).
- Vehicle type/model.
- Registration number.
- Capacity.
- Approval status.
- Availability status.
- Last location update.
- Action: View profile.

### Filters
- Approved / pending / rejected / correction required.
- Available / unavailable / offline.
- Vehicle type.
- Capacity range.
- Search by name, phone, partner ID, or registration number.

Use a directory that remains useful even when location is unavailable. The directory and the live matching results are related but distinct: the directory contains driver records, while the matching view contains currently eligible drivers for a particular request.

## 7.7 Driver Details

**Purpose:** Give the vendor a complete but concise operational view of one driver.

Sections:
- Driver profile: name, phone, date of birth, residential address, email if provided.
- KYC: driving licence and identity proof status/preview.
- Vehicle: type, model, registration, manufacturing year, load capacity, condition.
- Vehicle photo: full side-view image showing the complete truck body and all tyres.
- Vehicle documents: RC, insurance, fitness certificate, PUC, applicable permits.
- Current availability and last location update.
- Recent invitations and responses.

Sensitive document access must be restricted to authorised vendor users and handled securely. Display only what is necessary for review and operations.

## 7.8 Driver Applications — List and Review

**Purpose:** Review driver partners who have submitted onboarding information but are not yet approved.

### List view
- Driver name / partner ID.
- Submitted date.
- Vehicle type.
- Documents received count/status.
- Application status: Pending Review / Needs Information / Approved / Rejected.
- Action: Review.

### Review page
Present information in clear sections:
1. Personal information.
2. Driving licence.
3. Identity proof (as supported by the application).
4. Vehicle information.
5. Full side-view vehicle photo.
6. Vehicle documents.

### Review actions
- **Approve:** activate the driver after all required checks pass.
- **Request Correction:** choose missing/unclear item(s), add a concise note, and send it to the driver app.
- **Reject:** require a reason and record the decision.

The system must record reviewer, timestamp, decision, and reason. Never silently delete an application or document.

## 7.9 Activity

A compact audit-style feed of operational events:
- New request received.
- Driver invitation sent.
- Driver accepted/rejected.
- Vendor follow-up recorded.
- Driver application submitted.
- Application approved/rejected/returned for correction.

For the smallest MVP, Activity can be embedded in request and driver details. Do not create a separate page unless operators need a cross-request timeline.

## 7.10 Account & Support

Minimum scope:
- Vendor user's name and email/username.
- Change password.
- Sign out.
- Contact support details or support form.

No company billing, team management, role editor, or admin settings in the MVP.

---

## 8. Status model

Status names must be consistent across dashboard cards, lists, detail pages, notifications, and APIs.

### 8.1 Transport request statuses

Recommended MVP statuses:

| Status | Meaning | Vendor's next action |
|---|---|---|
| New | Customer request received; not yet reviewed. | Review request. |
| Ready to Match | Vendor has reviewed request and can find drivers. | Find drivers. |
| Invitations Sent | One or more driver invitations have been sent. | Monitor responses. |
| Awaiting Responses | At least one invitation is pending. | Wait or send more invitations if appropriate. |
| Driver Accepted | One or more drivers accepted. | Contact accepted driver(s). |
| Follow-up Needed | Driver accepted, vendor has not recorded contact outcome. | Call/contact driver and record result. |
| Confirmed / Assigned | Vendor has confirmed an operational arrangement with a driver. | Continue outside MVP or update manually. |
| Closed | Request no longer requires action. | View history. |
| Cancelled | Request was cancelled. | View reason/history. |
| No Driver Available | No suitable available drivers found or all invitations declined/expired. | Expand search or follow up with customer. |

A request can have several invited drivers. Its overall status should be derived from request state and invitation states, not from a single driver response alone.

### 8.2 Individual driver invitation statuses

- Pending
- Accepted
- Rejected
- Expired
- Failed to Send
- Cancelled by Vendor

### 8.3 Driver application statuses

- Draft (driver has not submitted)
- Submitted
- Pending Review
- Needs Information
- Approved
- Rejected

### 8.4 Driver availability statuses

- Available
- Busy / On Assignment (if the system has this signal)
- Unavailable
- Offline
- Location Stale

Do not infer “Available” solely from a GPS update. Availability must be an explicit driver-side state or backend eligibility state.

---

## 9. Data the vendor needs

### 9.1 Customer transport request

- Request ID.
- Customer ID and vendor-visible contact details.
- Pickup address and coordinates.
- Destination address and coordinates.
- Goods category: Furniture, Electronics, Construction Materials, Household Items, Packages, Other.
- Goods description.
- Quantity / item count.
- Approximate weight.
- Optional goods photos.
- Loading/unloading assistance required (yes/no).
- Fragile goods (yes/no).
- Special handling instructions.
- Created timestamp.
- Current request status.

### 9.2 Driver

- Driver ID / partner ID.
- Full name.
- Verified phone number.
- Optional email.
- Date of birth and residential address as captured during driver onboarding.
- KYC document references and review statuses.
- Driver application status.
- Availability status.
- Current/latest location coordinates and timestamp, where consent and permissions allow.

### 9.3 Vehicle

- Vehicle type.
- Model.
- Registration number.
- Manufacturing year.
- Load capacity (tons).
- Condition.
- Optional full side-view photo.
- RC, insurance, fitness, PUC, and applicable permit documents with expiry dates where available.

### 9.4 Driver invitation

- Invitation ID.
- Request ID.
- Driver ID.
- Sent timestamp.
- Response status.
- Response timestamp.
- Optional driver response note, if supported.

### 9.5 Vendor follow-up

- Request ID.
- Driver ID.
- Outcome status.
- Note.
- Vendor user ID.
- Timestamp.

---

## 10. Permissions and visibility

This MVP has one portal role: **Vendor**. There is no Admin role in the interface or routing scope.

| Data/action | Vendor portal | Driver app |
|---|---|---|
| Full customer request | View | Only job-relevant details |
| Customer phone/email | View for coordination | Hidden in initial invitation |
| Nearby driver location | View for matching if permitted/fresh | Driver's own location controls |
| Driver phone number | View after/when needed for operations | Own number |
| Driver KYC/vehicle documents | Review if vendor approval is assigned | Upload/view own submissions |
| Send driver invitation | Yes | No |
| Accept/reject invitation | No | Yes |
| Approve driver application | Yes, according to the current workflow | No |

Even with a single portal role, protect every page and backend endpoint with authenticated vendor access. Do not rely on hiding UI controls as an authorisation mechanism.

---

## 11. Dashboard content and layout recommendation

The Overview should answer five questions immediately:

1. How many new transport requests need review?
2. Which requests are waiting for driver responses?
3. Which accepted drivers need a vendor call?
4. How many approved drivers are available nearby/now?
5. Are there driver applications waiting for document review?

### Suggested layout

```text
┌──────────────────────────────────────────────────────────────────┐
│ Overview                                  Notifications  Account  │
├───────────────┬───────────────┬───────────────┬───────────────────┤
│ New Requests  │ Awaiting      │ Follow-up     │ Available Drivers │
│ count         │ Responses     │ Needed        │ count             │
├───────────────┴───────────────┴───────────────┴───────────────────┤
│ Requests Needing Attention                         View all       │
│ ID | Route | Goods | Received | Status | Open                    │
│ ...                                                              │
├──────────────────────────────────────┬───────────────────────────┤
│ Recent Activity                      │ Driver Availability       │
│ Request received                     │ Available drivers: count  │
│ Driver accepted                      │ Find drivers →            │
│ Application submitted                │                           │
└──────────────────────────────────────┴───────────────────────────┘
```

This is a structural recommendation, not a mandate to copy the ASCII layout literally. Use the existing website design system for spacing, typography, card treatment, and responsive behaviour.

---

## 12. UX rules for operational efficiency

- Make the request ID visible in list and detail views.
- Keep pickup and destination visible when navigating request details and driver matching.
- Preserve request context when returning from matching to the request.
- Place the main action next to the information needed to make that decision.
- Use a clear confirmation step before sending invitations to multiple drivers.
- Prevent duplicate actions caused by repeated clicks; use loading and success states.
- Make Accepted responses stand out from Pending and Rejected responses.
- Display time in the vendor's configured timezone and use clear absolute dates where ambiguity matters.
- Show empty states that explain the next useful action, e.g. “No available drivers within 10 km. Increase the search radius.”
- Use searchable tables and filters instead of large decorative dashboards.
- Avoid putting every data field into one enormous form or table. Group details by operational task.
- Keep maps secondary to the task; driver list cards must remain usable when map loading fails.
- Every action must have a real backend effect or be clearly disabled/not yet available.

## 13. Required screen states

Every major page must handle:

- Initial loading.
- Empty results.
- Search returns no matches.
- Network/API failure with retry.
- Permission denied / unauthorised.
- Stale location data.
- Partial document uploads.
- Invitation send failure.
- Duplicate invitation prevention.
- Driver accepts/rejects while vendor is viewing the page (refresh or real-time update).
- Driver application missing/unclear documents.

Use optimistic UI only where rollback is safe and the system can reconcile with the server. Do not show an invitation as successfully sent until the backend confirms it.

---

## 14. Notifications

For MVP, notifications should be operational and actionable:

- New transport request received.
- Driver accepted invitation.
- Driver rejected invitation.
- Driver invitation expired (if expiry is implemented).
- New driver application submitted.
- Driver uploaded requested corrections.

Each notification should open the exact request or driver application. A basic notification centre/dropdown is enough initially; complex notification preferences can wait.

---

## 15. Non-functional and integration requirements

### Security and privacy
- Use secure authentication and session handling.
- Enforce vendor authorisation on all backend endpoints.
- Protect KYC and vehicle document URLs; use authenticated or time-limited access.
- Do not leak customer contact details into driver invitation responses or logs visible to drivers.
- Log review decisions and operational status changes.
- Collect/share driver location only under the driver's explicit permission and availability state.
- Show location timestamp/freshness and handle revoked/background permissions.

### Performance
- Paginate request and driver lists.
- Load document previews only when needed.
- Avoid loading all drivers/locations into the browser at once; query by map bounds/radius and filters.
- Keep map interactions responsive; provide a list fallback.

### Reliability
- Use server-side timestamps for invitation and status events.
- Handle duplicate submission/retry safely.
- Keep invitation responses idempotent.
- Refresh status from the server when a page regains focus or a real-time event arrives.

### Responsive design
- Primary target: desktop/laptop operations users.
- Support tablet layouts.
- On smaller widths, stack map and driver list, convert wide tables to compact rows/cards, and keep primary actions reachable.
- Do not attempt to make the operational map/table workspace a phone-first dashboard; the driver and customer workflows live in mobile apps.

---

## 16. Recommended implementation order

Build in vertical slices so each release produces a working workflow.

### Slice 1 — Portal foundation
- Vendor login and logout.
- Protected layout and sidebar.
- Overview page shell.
- Vendor account menu.

### Slice 2 — Request intake and review
- Transport request list.
- Request detail page.
- Goods/route/photo display.
- Status labels and filters.

### Slice 3 — Driver matching
- Driver availability query.
- Map + list matching screen.
- Radius, count, vehicle type and capacity filters.
- Multi-select and send invitation.
- Invitation status list.

### Slice 4 — Driver response and vendor follow-up
- Pending/accepted/rejected responses.
- Notification/event updates.
- Call driver action.
- Follow-up note and outcome.

### Slice 5 — Driver verification
- Driver application list.
- Driver profile and document review.
- Approve / Request Correction / Reject.
- Audit event.

### Slice 6 — Operational polish
- Overview metrics and attention queue.
- Activity feed.
- Support/account page.
- Empty/error/loading states.
- QA across viewport sizes and permission states.

If driver approval must precede live matching from day one, build the minimum Driver Applications review flow before enabling invitations to real drivers.

---

## 17. Acceptance criteria for MVP

### Authentication
- Unauthenticated users cannot access portal pages.
- Valid vendor credentials open Overview.
- Invalid credentials show a clear error without leaking account existence unnecessarily.

### Requests
- Each customer request appears once with a stable ID.
- Request details show all submitted route, cargo and handling fields.
- Customer contact details are vendor-only.
- Status transitions are recorded with timestamps.

### Driver matching
- Matching returns only approved and available drivers by default.
- Results are filtered by radius and other selected criteria.
- Distance is calculated from pickup coordinates, not text-address assumptions.
- Location update time is visible.
- Selecting multiple drivers and sending invitations works end to end.
- The system prevents duplicate active invitations.

### Driver responses
- The vendor can distinguish Pending, Accepted and Rejected for every invited driver.
- Responses are associated with the correct request and driver.
- Accepted drivers expose the vendor contact action.
- Vendor can record a follow-up outcome.

### Driver verification
- Vendor can open every submitted document securely.
- Vendor can approve, request correction, or reject.
- Correction/rejection requires a reason.
- The decision is recorded and reflected in the driver app's account state.

### Quality
- No dead buttons or fake navigation.
- All major views have loading, empty, and error states.
- Customer and driver data is not exposed outside its intended role.
- No Admin functionality is included in the MVP UI.

---

## 18. Guidance for the coding agent

Treat this document as the product requirements source of truth for the Vendor portal MVP. Before implementing:

1. Inspect the existing repository, framework, routing, component system, API client, and authentication mechanism.
2. Inspect the existing design-system/agent-skill file and follow it for visual decisions. Do not create a new brand system.
3. Identify existing customer-request, driver, vehicle, document, location, and invitation APIs before inventing new models.
4. Reuse existing components and state management where practical.
5. Map existing backend capabilities to the sitemap. If an API is missing, report the missing contract instead of simulating success.
6. Implement the smallest complete vertical slice, starting with Vendor Login → Requests → Request Details → Find Drivers → Send Invitations → Responses.
7. Keep Admin routes and Admin-only settings out of the current scope.
8. Do not invent pricing, commissions, bidding, payments, or settlement logic.
9. Use realistic empty states and clear disabled states for backend functionality that is not available.
10. After implementation, verify core navigation, status updates, privacy boundaries, and responsive layouts.

### Implementation principle

**Build for the operator's next decision, not for the maximum amount of information that can fit on a dashboard.**

The vendor's central job is to move each transport request forward by finding a suitable driver and following up on driver responses. Every top-level page, data point, filter, and button should support that job.

---

## 19. Research references and product patterns

The following official product/help resources were reviewed as reference points. They are used for general interaction patterns, not as a requirement to copy their full feature sets.

1. **Uber Freight — Using the Web Portal**  
   https://help.uber.com/am/freight/carrier/article/de-webportal-van-uber-freight?nodeId=d36342d0-4b17-49fc-9c2a-5b360b7d0903  
   Relevant pattern: carrier portal search by pickup location/date/radius, delivery location/radius and equipment type; a separate “My Loads” view for checking load status; document upload in the portal.

2. **Uber Freight — Searching and Booking Loads**  
   https://help.uber.com/so/freight/carrier/article/using-the-uber-freight-app-to-search-and-book-loads?nodeId=dfe69814-ea0d-43e8-ad16-f77621cd2e0c  
   Relevant pattern: load discovery filters such as location, equipment/trailer type, pickup date, haul length, weight and pricing; load details before committing; separate current/booked and past views. The current MVP does not adopt its bidding or payment workflow.

3. **Truckstop — Load Searching**  
   https://community.truckstop.com/articles/Knowledge/Searching-for-loads  
   Relevant pattern: a focused search flow beginning with origin and equipment type, followed by matching results.

4. **uShip — Broker Basics**  
   https://help.uship.com/hc/en-us/articles/115007641508-Broker-Basics  
   Relevant pattern: a broker may find carriers for shipments, directly assign a shipment or post it for carriers, and manage posted/assigned work separately. This supports separating request intake, driver discovery, invitation, and response management. The MVP uses explicit driver invitations rather than a public bidding marketplace.

5. **uShip — Welcome for Carriers**  
   https://www.uship.com/learn/ca/carriers/welcome/  
   Relevant pattern: carrier profiles, shipment search, map-based location filters, and notifications for relevant work.

6. **Porter Driver Partner App — Google Play listing**  
   https://play.google.com/store/apps/details?id=com.theporter.android.driverapp  
   Relevant pattern: driver onboarding with documents and vehicle association, location-aware driver operations, order requests, verification status, support, and order history. The listing is a product reference; the Vendor portal should only implement the vendor-side review and operations required by this product.

### Research interpretation

Competitor products vary: some are open marketplaces with bids and payments; others are managed dispatch networks or carrier portals. This product's MVP is a **managed matching and coordination workflow**. Borrow useful patterns—search filters, map/list matching, request status, invitation results, document review, and activity history—without importing the complexity of public listings, competitive bidding, settlements, or full transport execution.

---

## 20. Final MVP sitemap summary

| Priority | Page | Why it exists |
|---|---|---|
| P0 | Vendor Login | Secure entry into the vendor workspace. |
| P0 | Overview | Shows requests and actions requiring attention. |
| P0 | Transport Requests | Lists all customer-submitted requirements. |
| P0 | Request Details | Lets vendor inspect route, goods, photos, and customer contact. |
| P0 | Find Drivers | Finds nearby approved/available drivers and supports multi-select invitations. |
| P0 | Invitation Responses | Shows accepted/rejected/pending responses per driver; can be a section of Request Details. |
| P0 | Driver Applications | Enables vendor verification and approval of driver partners. |
| P0 | Driver Application Review | Securely reviews personal, KYC, vehicle and document information. |
| P1 | Drivers Directory | Search and inspect approved and existing drivers. |
| P1 | Driver Details | Consolidated driver, vehicle, documents, location and invitation history. |
| P1 | Activity | Cross-request operational event feed; may initially be embedded in details. |
| P1 | Account & Support | Basic vendor profile, password and help access. |

**Recommended first build:** Vendor Login → Overview → Transport Requests → Request Details → Find Drivers → Send Invitations → Driver Responses. Build Driver Applications early enough to ensure only approved drivers enter the matching pool.
