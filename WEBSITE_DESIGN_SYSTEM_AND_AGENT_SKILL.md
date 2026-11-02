---
name: green-product-web-design
version: 1.0.0
description: A reusable frontend design and implementation skill for building distinctive, minimal, Apple-influenced websites and web applications with a disciplined emerald-green design system and anti-generic-AI standards.
triggers:
  - build or design a website
  - create or redesign a web page
  - implement a SaaS dashboard or admin interface
  - create a responsive frontend screen
  - improve visual quality, UX, motion, or design consistency
---

# Green Product Web Design System & Frontend Agent Skill

> **Purpose:** Give the coding agent one authoritative source for visual direction, UX decisions, frontend implementation, motion, accessibility, and quality control. Use this file before creating or modifying any website or web application screen.
>
> **Core standard:** The result should feel intentional, calm, premium, clear, and product-specific—like a carefully designed modern Apple-quality product, not a generic AI-generated template.

---

# Part I — The design philosophy

## 1. The standard to aim for

Create interfaces that are:

- **Clear before clever.** A visitor should understand what the page is and what to do next without decoding the interface.
- **Minimal, not empty.** Remove visual noise, not useful information. Whitespace must establish hierarchy or give content room to breathe.
- **Distinctive, not decorated.** Give each page one or two deliberate visual ideas rooted in the product, audience, or content. Do not add random visual effects to make a page appear designed.
- **Consistent, not repetitive.** Reuse tokens and components, but do not force every section into the same card or grid layout.
- **Responsive by design.** Desktop, tablet, and mobile should each feel composed for their viewport—not like a desktop page squeezed smaller.
- **Useful in every state.** Design the loading, empty, error, disabled, selected, success, and permission states where they apply—not only the ideal screenshot.
- **Polished in details.** Typography, alignment, spacing, icons, field behavior, focus, transitions, and content quality must all agree.

## 2. Apple-influenced, not Apple-copied

Use the qualities associated with excellent Apple interface design:

- Strong hierarchy and restrained typography.
- Generous, purposeful whitespace.
- A small number of clearly differentiated surfaces.
- Precise alignment and consistent spacing.
- Natural, fast, understated motion.
- Controls that look and behave like controls.
- Content that stays in focus while the interface stays quiet.
- Helpful feedback and obvious interaction affordances.

Do **not** copy Apple's proprietary screens, icons, branding, or layouts. Translate the underlying principles into this product's own identity.

## 3. The four design disciplines to combine

Use these references as complementary disciplines, not as four styles to mix together.

### UI/UX Pro Max — design intelligence
Repository: https://github.com/nextlevelbuilder/ui-ux-pro-max-skill

Use its design-system mindset:
- Select patterns based on product category, audience, platform, and task.
- Define color roles, typography, spacing, surfaces, components, and states before implementation.
- Consider accessibility, responsiveness, and platform conventions.
- Keep iconography consistent and purposeful.

### Taste — art direction and composition
Repository: https://github.com/leonxlnx/taste-skill

Use its art-direction mindset:
- Derive the visual idea from the actual subject, not from a category template.
- Decide composition, type, palette, imagery, and section rhythm intentionally.
- Make every section carry useful content and a clear point.
- Avoid a distinctive hero followed by generic repeated sections.
- Treat marketing pages and operational app screens as different design problems.

### Anti-UI-Slop — product-specific craft
Repository: https://github.com/uizze/uizze

Use its anti-slop mindset:
- Inspect the existing product, codebase, assets, and design language before changing anything.
- Preserve what already works.
- Make decisions based on real user tasks and real product information.
- Avoid decorative metrics, fake dashboards, arbitrary cards, and irrelevant fashionable patterns.
- Review the full experience, including states and edge cases.

### Anthropic Frontend Design — frontend art direction
Repository: https://github.com/anthropics/skills/tree/main/skills/frontend-design

Use its frontend design mindset:
- Make deliberate decisions about typography, palette, layout, density, and emphasis.
- Give the page a distinctive composition instead of defaulting to familiar AI layouts.
- Use motion to communicate state or hierarchy, never as decoration alone.
- Keep semantic content, accessibility, and responsive intent intact.

**Important:** When the actual skill packages or scripts are installed in the environment, read their current `SKILL.md` files and relevant references before using them. Do not claim to have run a skill, design search, browser inspector, or reference script unless the tool was actually available and executed.

---

# Part II — Locked visual system

## 4. Brand direction

**Visual personality:** dependable, modern, calm, premium, approachable, efficient, and transport-oriented.

**Primary visual language:** emerald green + white + near-black, supported by restrained mint surfaces and cool neutral borders.

The website must share the same visual identity as the mobile applications. It may use different layout patterns for desktop, but it must not look like a different company.

### 4.1 Color tokens

These values are the source of truth. Define them once as CSS variables or the project's existing token format.

| Token | Hex | Use |
|---|---|---|
| `--color-brand-forest` | `#0B6046` | Deep green fields, dark brand sections, selected dark surfaces |
| `--color-brand-primary` | `#087D55` | Primary actions, active navigation, selected controls, key links |
| `--color-brand-strong` | `#066B49` | Hover/pressed state and stronger emphasis |
| `--color-brand-soft` | `#EAF4EF` | Soft green surfaces, selected rows, quiet callouts |
| `--color-brand-tint` | `#F2F8F4` | Very subtle green background tint where needed |
| `--color-bg` | `#FFFFFF` | Default page background |
| `--color-surface` | `#FFFFFF` | Main cards, panels, menus |
| `--color-surface-muted` | `#F6F8F7` | Quiet secondary surfaces |
| `--color-text-primary` | `#111B18` | Headings, primary content, important values |
| `--color-text-secondary` | `#66716C` | Body support text and secondary content |
| `--color-text-muted` | `#707A76` | Placeholder, metadata, low-priority copy only |
| `--color-border` | `#D6E0DB` | Inputs, dividers, quiet card outlines |
| `--color-border-brand` | `#16845E` | Focused fields and selected control outlines |
| `--color-success` | `#16834F` | Success and completed states |
| `--color-warning` | `#A96B13` | Pending / attention states; use sparingly |
| `--color-danger` | `#B42318` | Destructive and error states |
| `--color-white` | `#FFFFFF` | Text/icons on dark green surfaces |

**Contrast rule:** Use `#66716C` or darker for essential secondary body text on white. Reserve `#707A76` for less important metadata or large text. Never use pale gray for essential content.

### 4.2 Color usage proportions

Treat the palette as a hierarchy, not a paint bucket:

- White / near-white should form most of the interface.
- Near-black should carry most text and important content.
- Brand green should be concentrated on the main action, selected state, key brand area, and a small number of meaningful highlights.
- Mint should distinguish a quiet surface or selected state, not fill every card.
- Semantic colors should only communicate actual states.

Do not make every section green. Do not put every icon inside a tinted square. Do not use green gradients to create fake depth.

### 4.3 CSS token starter

Use the existing project's token system if one already exists. Otherwise create a single source of truth such as:

```css
:root {
  color-scheme: light;
  --color-brand-forest: #0B6046;
  --color-brand-primary: #087D55;
  --color-brand-strong: #066B49;
  --color-brand-soft: #EAF4EF;
  --color-brand-tint: #F2F8F4;
  --color-bg: #FFFFFF;
  --color-surface: #FFFFFF;
  --color-surface-muted: #F6F8F7;
  --color-text-primary: #111B18;
  --color-text-secondary: #66716C;
  --color-text-muted: #707A76;
  --color-border: #D6E0DB;
  --color-border-brand: #16845E;
  --color-success: #16834F;
  --color-warning: #A96B13;
  --color-danger: #B42318;

  --font-sans: -apple-system, BlinkMacSystemFont, "SF Pro Display",
    "SF Pro Text", Inter, "Segoe UI", sans-serif;

  --space-1: 4px;
  --space-2: 8px;
  --space-3: 12px;
  --space-4: 16px;
  --space-5: 20px;
  --space-6: 24px;
  --space-8: 32px;
  --space-10: 40px;
  --space-12: 48px;
  --space-16: 64px;
  --space-20: 80px;
  --space-24: 96px;

  --radius-sm: 8px;
  --radius-md: 12px;
  --radius-lg: 16px;
  --radius-xl: 20px;
  --radius-2xl: 24px;
  --radius-pill: 999px;

  --shadow-soft: 0 8px 28px rgba(17, 27, 24, 0.06);
  --shadow-floating: 0 12px 36px rgba(17, 27, 24, 0.12);

  --content-width: 1200px;
  --content-width-wide: 1320px;
  --page-gutter: clamp(20px, 4vw, 56px);

  --motion-fast: 140ms;
  --motion-standard: 200ms;
  --motion-slow: 280ms;
  --ease-standard: cubic-bezier(0.2, 0.8, 0.2, 1);
}
```

Avoid adding dozens of arbitrary one-off variables. Extend this small system only when a real product requirement justifies it.

## 5. Typography

### 5.1 Font family

Preferred direction: a clean, native-feeling sans serif.

```css
font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display",
  "SF Pro Text", Inter, "Segoe UI", sans-serif;
```

Use an already-installed project font if it is coherent with the system. Do not add a new font dependency just because a page needs visual interest. Do not use more than two font families without a clear editorial reason.

### 5.2 Web type scale

Use responsive sizes with `clamp()` where appropriate.

| Role | Desktop range | Weight | Notes |
|---|---:|---:|---|
| Display / hero | 48–68px | 650–750 | Use only for a true hero; keep line length controlled |
| H1 | 40–56px | 650–720 | One per page; state the page's purpose clearly |
| H2 | 30–40px | 600–700 | Section heading |
| H3 | 22–28px | 600–650 | Subsection / card heading |
| H4 | 18–20px | 600 | Compact group heading |
| Body large | 18–20px | 400–450 | Hero support / lead paragraph |
| Body | 15–17px | 400–450 | Default reading size |
| Small | 13–14px | 400–500 | Secondary metadata only |
| Button | 14–16px | 550–650 | Clear action label |

Mobile display and heading sizes must reduce intentionally; do not let large desktop headlines wrap into four or five lines on a phone.

### 5.3 Type rules

- Use sentence case for interface copy.
- Keep headings direct and useful.
- Prefer one strong heading over a label + eyebrow + subheading + badge stack.
- Body text should have comfortable line-height, usually `1.45–1.65`.
- Large headings should have tighter line-height, usually `1.0–1.12`.
- Control line length. Avoid long full-width paragraphs.
- Use bold weights for hierarchy, not for every label.
- Do not use letter-spaced tiny uppercase as a default decoration.
- Do not use monospace outside code, technical IDs, or data where tabular alignment genuinely helps.

## 6. Spacing, grid, and page frame

### 6.1 Spacing rhythm

Use a consistent 4px base and an 8px primary rhythm:

`4 / 8 / 12 / 16 / 20 / 24 / 32 / 40 / 48 / 64 / 80 / 96`

Do not choose margins independently for every element. Reuse the scale.

### 6.2 Container widths

- Reading / marketing content: `max-width: 1120–1200px`.
- Wide operational SaaS content: `max-width: 1280–1360px` when data requires it.
- Form-focused content: `max-width: 520–640px`.
- Long-form text: `max-width: 680–760px`.
- Use equal page gutters at both edges.

### 6.3 Grid

- Use a 12-column desktop grid where helpful; do not impose it on every component.
- Use 2–3 columns only when the content genuinely compares or groups at that scale.
- Tablet layouts should simplify before they become cramped.
- Mobile layouts should become one column unless there is a strong reason not to.
- Never let a section sit on one side of a wide viewport with an unexplained empty half.
- Asymmetry is allowed inside a balanced page frame.

### 6.4 Responsive breakpoints

Use the project's existing breakpoints where defined. Otherwise a practical starting point is:

- Small mobile: up to 480px.
- Mobile / large mobile: 481–639px.
- Tablet: 640–899px.
- Small desktop: 900–1199px.
- Wide desktop: 1200px and above.

Do not target breakpoints mechanically. Add a breakpoint when the layout needs it, and check intermediate widths rather than only the named breakpoints.

## 7. Surfaces, borders, corners, and shadows

- Use white as the default primary surface.
- Use subtle mint only to distinguish a selected, informational, or status surface.
- Use borders to define inputs, table boundaries, or meaningful groupings—not as decoration everywhere.
- Use consistent radii: 8 / 12 / 16 / 20 / 24px.
- Use pill radii only for primary brand CTAs, segmented choices, tags that truly need pill shape, or a deliberately floating mobile navigation control.
- Avoid giving every card a large radius. Repeated soft cards can make the page look like a generated template.
- Avoid shadows on static content. Reserve elevation for floating menus, dialogs, popovers, or a floating navigation surface.
- Prefer tonal contrast and spacing over borders plus shadows plus background tints all at once.

---

# Part III — UX and component behavior

## 8. Every page starts with a user task

Before writing markup, state internally (or in a short implementation note):

1. Who is using this page?
2. What are they trying to accomplish?
3. What information do they need before acting?
4. What is the single primary action?
5. What can be deferred or removed?
6. What states and errors can occur?

If an element does not help the user complete the task, understand the product, or navigate, question whether it belongs.

## 9. Navigation

### Marketing / public website

- Keep navigation clear and compact.
- Use the wordmark, a small number of meaningful links, and one primary action.
- Make current-page context obvious.
- Mobile navigation must have a real open/close state, keyboard support, and predictable dismissal.
- Do not use a floating pill navigation unless the design specifically calls for it.

### SaaS / operational application

- Keep primary app navigation stable across screens.
- Distinguish global navigation from page-level actions.
- Use active states with the brand green, but do not color every navigation item green.
- Use labels alongside icons when the meaning would otherwise be ambiguous.
- Preserve navigation structure during feature additions; do not invent a new navigation model for each page.

## 10. Buttons

### Primary action

- Use `--color-brand-primary` with white text.
- For a prominent CTA, use a pill or moderately rounded shape consistent with the existing product.
- Keep the label action-oriented: `Continue`, `Submit request`, `Save changes`, `View details`.
- Use one primary button per meaningful task area.
- Add an arrow only when it signals forward movement or the established brand convention requires it.

### Secondary action

- Use a quiet outline, neutral surface, or plain text action.
- Do not style every button as primary.
- Destructive actions must be visibly distinct and require appropriate confirmation.

### States

Every interactive button must have:
- Default.
- Hover (web pointer only; no hover-only information).
- Keyboard focus-visible.
- Pressed / active.
- Disabled when the action cannot be performed.
- Loading when an asynchronous task is in progress.

Never use an inert button that looks functional. Every button must perform its stated action.

## 11. Form fields

- Use visible labels for fields; placeholders are not a substitute for labels.
- Use consistent control heights and alignment.
- Keep field descriptions close to the relevant field.
- Use the right input type, autocomplete hint, and input mode.
- Group related fields logically; do not put the whole form into a pile of cards.
- Mark optional fields clearly and do not imply they are required.
- Place validation messages beside the field they describe.
- Preserve entered values when validation fails or when users navigate back.
- Support keyboard navigation and visible focus.
- Do not use icons inside every field without purpose.

## 12. Cards, lists, and data

Choose the pattern that best matches the data:

- **Card:** independent item with a meaningful action or content boundary.
- **List:** repeated items that are primarily scanned or compared.
- **Table:** structured data with shared columns or sorting needs.
- **Timeline:** ordered events or workflow progress.
- **Map:** real location-based work where geography matters.
- **Form:** data collection where input sequence matters.

Do not turn every section into a card grid. Do not add a chart if the user needs a number, a status, or a sortable list. Never invent KPIs, customer quotes, ratings, savings, activity, or fake data to fill empty space.

## 13. Statuses and feedback

Use semantic colors consistently:

- Green: success, completed, approved, active.
- Amber: pending, needs attention, waiting, under review.
- Red: error, rejected, destructive action.
- Neutral gray: inactive, not started, secondary information.

Always pair color with text or an icon; never rely on color alone.

Provide useful feedback for:
- Loading / processing.
- Empty state.
- Validation error.
- Submission success.
- Permission denied.
- Offline / unavailable state.
- Unsaved changes when relevant.

Do not show fake progress or artificial time estimates. Never invent an approval deadline, system status, or successful action that the backend has not confirmed.

## 14. Icons and illustration

- Use one coherent icon family, preferably a consistent outline set.
- Keep stroke weights and sizes consistent.
- Do not mix filled, outline, 3D, emoji, and hand-drawn icon styles in one interface.
- Icons should support recognition, not act as decoration.
- Use editorial illustration only when it communicates the product's subject or helps an empty/success/welcome state.
- Do not reuse the same illustration repeatedly across unrelated screens.
- Do not place an illustration on every page merely to fill whitespace.
- Never invent product screenshots or pretend conceptual mockups are real product UI.

## 15. Content and microcopy

- Use plain, specific language.
- Write the actual heading and action the user needs—not generic filler.
- Avoid fabricated testimonials, user counts, logos, ratings, statistics, and trust claims.
- Use realistic example values only when a design needs sample content; label them as examples or keep them obviously fictional.
- Avoid empty marketing phrases such as “revolutionary,” “next-generation,” “seamless,” and “world-class” unless supported by the brief.
- Keep helper text short and actionable.
- Ensure the content fits the component and viewport; do not shrink text to make an overloaded layout fit.

---

# Part IV — Motion and interaction quality

## 16. Motion philosophy

Motion should make the interface easier to understand. It should communicate a state change, reveal a relationship, provide feedback, or establish hierarchy.

Use motion sparingly. A good interface still looks complete with all animation disabled.

### 16.1 Default motion values

- Fast feedback: 120–160ms.
- Standard transition: 180–220ms.
- Larger panel / dialog transition: 220–280ms.
- Use `cubic-bezier(0.2, 0.8, 0.2, 1)` or a similarly restrained ease.
- Prefer opacity and small transforms over animating layout dimensions.
- Keep travel distance subtle: usually 2–8px for small interface elements.

### 16.2 Appropriate motion

- Button press: a subtle scale or color shift.
- Dropdown / menu: short fade and slight translate.
- Dialog / sheet: controlled entrance and exit, with backdrop fade.
- Navigation: clear selected-state change.
- Form feedback: short inline state transition.
- Progress: animate only when actual progress changes.
- Page transition: brief, consistent transition only if it improves orientation.

### 16.3 Avoid

- Scroll-jacking.
- Large parallax on utility pages.
- Repeated floating / bobbing elements.
- Long entrance animations that delay access to content.
- Animating every element independently.
- Auto-playing videos with sound.
- Animation that hides content until scripts finish.
- Motion used to disguise weak hierarchy or unfinished composition.

### 16.4 Reduced motion

Respect the operating-system preference:

```css
@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    scroll-behavior: auto !important;
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}
```

Do not remove essential state feedback for users who prefer reduced motion; replace motion with a clear static state change.

---

# Part V — Page types and visual direction

## 17. Marketing / public pages

A marketing page is read and scrolled. It must make a clear argument, not merely display a sequence of fashionable sections.

Before implementation:
- Identify the audience and the page's conversion goal.
- Write the page argument as a sequence of claims and supporting proof.
- Build only sections that support that argument.
- Derive the visual concept from something specific to the product, its workflow, its people, or its output.
- Make the first viewport explain what the product is, who it is for, and what the visitor can do next.

Do not automatically use this sequence:

`Centered hero → logo strip → three icon cards → testimonial → pricing → FAQ → CTA`.

That sequence is allowed only when the content and conversion strategy genuinely justify it.

### Hero requirements

- One clear headline.
- One supporting sentence explaining the offer.
- One primary action and, only if needed, one secondary action.
- A meaningful visual: real product UI, relevant illustration, or photography that belongs to the subject.
- Avoid tiny eyebrow labels, random badges, fake customer logos, and abstract gradient blobs.
- The hero should not depend on animation to make sense.

### Section rhythm

- Give neighboring sections different compositions where their content differs.
- Use a real product screenshot, workflow, comparison, explanation, customer scenario, or proof point when available.
- Do not create a section with no useful claim.
- Do not leave huge empty areas simply to look minimalist.
- Do not place every paragraph inside a card.

## 18. SaaS / admin / operational interfaces

A SaaS app is worked in, not simply read. Prioritize task completion, scanability, and information relationships.

- Use a compact application shell with predictable navigation.
- Use a clear page title and concise action area.
- Put the primary task and important status where users can see them quickly.
- Make filters, sorting, pagination, and search behave consistently.
- Use tables or dense lists when they are the right tool; do not turn every row into a large card.
- Use quiet borders, tonal separation, and restrained elevation.
- Use data visualization only when it reveals a pattern or supports a decision.
- Label every metric and use consistent units, dates, and number formatting.
- Use empty states to explain what is missing and what action creates the first record.
- Provide clear loading, error, and retry states.
- Support responsive adaptation without hiding critical data or actions.

## 19. Forms, onboarding, and settings

- Keep one main task per screen when a multi-step workflow is appropriate.
- Show progress for genuinely multi-step flows; do not invent step counts.
- Preserve entered data across navigation and validation errors.
- Keep optional fields visibly optional.
- Explain why sensitive information or permissions are needed at the point they are requested.
- Put legal consent unchecked by default.
- Use plain-language permission explanations before opening browser/device prompts.
- Make save, cancel, and back behavior predictable.

## 20. Mobile web

- Use the same brand tokens but redesign layout for the narrow viewport.
- Use a mobile navigation pattern appropriate to the product.
- Keep primary controls within comfortable thumb reach where possible.
- Maintain at least 44×44px touch targets.
- Avoid horizontal scrolling except for genuinely wide data that has a clear overflow treatment.
- Respect safe areas when the web app behaves like an installed app.
- Avoid making desktop navigation simply wrap onto multiple lines.

---

# Part VI — Anti-UI-slop rules

## 21. Patterns to challenge before using

The following are warning signs. They are not absolute bans, but every use must have a product reason:

- Centered oversized heading above three identical cards.
- Rows of icon-in-colored-square feature cards.
- Floating glass panels and backdrop blur used without a clear layering need.
- Purple/blue gradients or gradient buttons added by habit.
- Glowing borders, gradient text, blobs, sparkles, and decorative noise.
- Too many pill badges, eyebrow labels, or uppercase micro-labels.
- Arbitrary 01/02/03 numbering.
- Thin rules and grids used as costume rather than structure.
- Large rounded cards around every small piece of content.
- Random charts, invented metrics, fake activity, and fake testimonial blocks.
- Generic 3D illustrations or stock imagery unrelated to the product.
- Decorative icons repeated next to every heading.
- Excessive shadows and multiple overlapping surface treatments.
- Tiny low-contrast secondary copy.
- Placeholder text presented as real content.
- Motion on every element.

## 22. The replacement test

For each visual choice, ask:

1. Does it help explain the product or complete the task?
2. Does it belong to this product rather than almost any unrelated website?
3. Is there a simpler pattern that communicates the same information?
4. Does it respect the established design system?
5. Would the page still look complete if this decoration were removed?

If the answer to the first two questions is no, remove or replace it.

## 23. Minimalism quality test

Minimalism is successful when:
- The primary action is obvious.
- Important information is present and readable.
- Related content is grouped logically.
- Whitespace separates meaningful groups.
- The page does not feel unfinished.

Minimalism has failed when:
- Users must guess what a field or icon means.
- Large blank sections exist without a compositional reason.
- Important content is hidden to preserve a sparse screenshot.
- Controls are too subtle to discover.
- The page has lost substance while retaining decoration.

---

# Part VII — Coding-agent implementation instructions

## 24. Mandatory repository inspection before editing

Before writing or modifying code:

1. Inspect the project structure and identify the framework, router, styling method, component library, and build system.
2. Read the package manifest and lockfile. Do not assume dependencies.
3. Find existing design tokens, global styles, shared components, layouts, icons, and assets.
4. Identify current route structure, data flow, state management, and API boundaries relevant to the requested page.
5. Inspect the current page at desktop and mobile sizes when a browser or screenshot tool is available.
6. Record what must be preserved: working interactions, URLs, forms, data, permissions, responsive behavior, and design patterns.

Do not replace the framework, router, styling system, or state management just to make the screen easier to implement. Avoid broad refactors when the request is limited to a page or component.

## 25. Implementation rules

- Use the project's existing stack and conventions.
- Reuse existing components when they are sound; improve them only when necessary.
- Centralize color, typography, spacing, radius, and motion tokens.
- Keep component responsibilities clear and avoid a monolithic page component.
- Use semantic HTML elements: `header`, `nav`, `main`, `section`, `form`, `label`, `button`, `footer` where appropriate.
- Use actual links for navigation and actual buttons for actions.
- Connect visible controls to real behavior. Never leave fake or inert interactions.
- Preserve existing API contracts and data structures unless the task explicitly changes them.
- Use real backend data where available. Do not invent permanent production values.
- Keep state close to the component or feature that owns it; use the project's existing global state approach when appropriate.
- Handle asynchronous work with loading, success, and failure states.
- Avoid unnecessary dependencies. Add a package only when its value is clear and the existing stack cannot reasonably provide the behavior.
- Do not hardcode secrets, credentials, personal data, or environment-specific URLs.
- Avoid broad CSS selectors that unintentionally affect unrelated pages.
- Keep code readable, typed where the project supports TypeScript, and documented where non-obvious behavior exists.

## 26. CSS / styling rules

- Prefer design tokens over repeated raw values.
- Prefer layout primitives (`grid`, `flex`, `gap`) over arbitrary absolute positioning.
- Use absolute positioning only for intentional overlays, illustration composition, or anchored controls.
- Avoid fixed heights for content containers unless the design requires a bounded region.
- Avoid using margin hacks to align unrelated elements.
- Use `min-width: 0` and sensible wrapping to prevent overflow in grid/flex layouts.
- Use `:focus-visible` for keyboard focus styling.
- Avoid `!important` except for narrowly justified overrides or reduced-motion safeguards.
- Keep responsive rules close to the component when that improves maintainability.
- Do not add a second CSS framework or design system when the project already has one.

## 27. Suggested reusable component inventory

Create only the components needed by the actual page. A typical reusable set may include:

- `Container` / page shell.
- `SiteHeader` / `AppSidebar` / `MobileNavigation` as appropriate.
- `Button` with primary, secondary, subtle, and destructive variants.
- `TextField`, `Select`, `Checkbox`, `RadioGroup`, `Textarea`.
- `Card` only for meaningful content grouping.
- `StatusBadge` with semantic variants.
- `EmptyState`, `ErrorState`, `LoadingState`.
- `Dialog`, `Popover`, `DropdownMenu` when real behavior requires them.
- `Toast` or inline feedback pattern.

A component should become reusable when it has a stable purpose or appears in more than one place. Do not abstract one-off markup prematurely.

## 28. Accessibility requirements

- Use semantic landmarks and heading order.
- Every form control must have a programmatic label.
- Interactive elements must be keyboard operable.
- Focus must be clearly visible.
- Do not use color alone to communicate status.
- Provide text alternatives for meaningful images; decorative images should have empty alt text.
- Use accessible names for icon-only buttons.
- Keep contrast appropriate for text and controls.
- Support browser zoom and text resizing.
- Respect `prefers-reduced-motion`.
- Use correct roles and ARIA only when native HTML semantics are insufficient.
- Manage focus when opening and closing dialogs or menus.

## 29. Responsive verification

Check the page at representative widths, adjusting for the actual target audience and content:

- 390px — small modern phone.
- 430px — larger phone.
- 768px — tablet portrait / compact layout.
- 1024px — small laptop / tablet landscape.
- 1440px — desktop.

Verify:
- No horizontal overflow.
- No clipped headings, buttons, or form controls.
- Navigation remains usable.
- Content order remains logical.
- Tables and wide content have an intentional overflow strategy.
- Touch targets remain usable.
- The page does not become a compressed desktop screenshot on mobile.

## 30. Motion implementation example

Use the existing motion library if one is already installed and appropriate. Otherwise CSS transitions are sufficient for most interfaces.

```css
.interactive-control {
  transition:
    background-color var(--motion-standard) var(--ease-standard),
    border-color var(--motion-standard) var(--ease-standard),
    color var(--motion-standard) var(--ease-standard),
    transform var(--motion-fast) var(--ease-standard);
}

.interactive-control:active {
  transform: scale(0.985);
}

.interactive-control:focus-visible {
  outline: 3px solid color-mix(in srgb, var(--color-brand-primary) 30%, transparent);
  outline-offset: 3px;
}

.reveal-in {
  animation: reveal-in 220ms var(--ease-standard) both;
}

@keyframes reveal-in {
  from { opacity: 0; transform: translateY(5px); }
  to { opacity: 1; transform: translateY(0); }
}

@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after {
    scroll-behavior: auto !important;
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}
```

Do not apply `.reveal-in` to every element. Use it only where the entrance adds orientation or hierarchy.

---

# Part VIII — Required design workflow

## 31. Follow this sequence for every request

### Step 1 — Classify the deliverable

Determine whether the user wants a marketing page, product dashboard, admin workflow, form, account page, or a specific component. A page that is read and scrolled needs a different composition from a screen that is operated throughout the day.

### Step 2 — Understand the task

Extract the audience, goal, content, required actions, existing assets, and constraints. Use already-provided information. Do not make the user repeat it.

### Step 3 — Inspect the current product

Read the existing code and design system. Preserve the established visual language and working functionality. Do not start from a generic template.

### Step 4 — Write a compact design direction

Before implementation, decide:
- Page purpose and primary user task.
- Visual concept specific to this product.
- Content hierarchy and page argument.
- Palette and token roles.
- Typography and type hierarchy.
- Layout / composition.
- Component and surface treatment.
- Image / illustration strategy.
- Motion strategy.

### Step 5 — Reject generic design choices

Run the anti-slop checks in Part VI. Replace generic patterns with layouts that fit the content and product.

### Step 6 — Implement the structural layout first

Build the page without depending on motion. Get the container, grid, headings, sections, and content density right before micro-interactions.

### Step 7 — Apply tokens and component styling

Use the design system consistently. Avoid local one-off colors, sizes, radii, and shadows.

### Step 8 — Implement real behavior

Connect forms, buttons, links, tabs, filters, navigation, uploads, and dialogs to their actual behavior. Preserve data through back/forward transitions where required.

### Step 9 — Add restrained motion

Add only the transitions that improve clarity or feedback. Respect reduced motion.

### Step 10 — Inspect and refine

When browser tooling is available, run the page and inspect real screenshots. Review at desktop, tablet, and mobile sizes. Fix the three most important visual or functional issues first, then repeat the inspection.

### Step 11 — Verify interactions

Test navigation, forms, keyboard access, validation, disabled states, loading, error handling, and the main task flow. Do not claim tests that were not run.

### Step 12 — Report honestly

Summarize what changed, what functionality works, what viewports were checked, and what remains incomplete. Distinguish implemented behavior from visual-only proposals.

---

# Part IX — Definition of done

## 32. Visual quality checklist

- [ ] The page has a clear purpose and a visible primary action.
- [ ] The design feels specific to the product, not like a generic generated template.
- [ ] The locked green / white / near-black palette is used consistently.
- [ ] Typography has a clear hierarchy and readable line lengths.
- [ ] Spacing follows the shared rhythm.
- [ ] Alignment and page gutters are consistent.
- [ ] Cards, borders, shadows, and rounded corners are used only where they help.
- [ ] There are no unnecessary gradients, glows, glass panels, icon-card grids, or decorative blobs.
- [ ] Content is complete enough to explain the page; it is not artificially sparse.
- [ ] Imagery is relevant, coherent, and not repeated without reason.
- [ ] The mobile composition has been deliberately designed.

## 33. UX and functionality checklist

- [ ] Every visible action has a real destination or behavior.
- [ ] Labels, helper text, and button copy are understandable.
- [ ] Forms support validation and preserve entered data appropriately.
- [ ] Loading, empty, error, and success states exist where needed.
- [ ] Keyboard focus is visible and logical.
- [ ] Interactive controls have usable target sizes.
- [ ] Color is not the only status indicator.
- [ ] Reduced-motion preferences are respected.
- [ ] No unrelated functionality or working route was broken.

## 34. Responsive and implementation checklist

- [ ] No horizontal overflow at tested widths.
- [ ] Header, navigation, forms, and content adapt to mobile.
- [ ] Existing framework and architecture were preserved.
- [ ] Shared design tokens are centralized.
- [ ] No unnecessary dependencies were introduced.
- [ ] Browser console and build output have been checked when tools are available.
- [ ] The production build was verified when the project supports it.
- [ ] The final report accurately states what was and was not tested.

---

# Part X — Reference and collaboration rules

## 35. Reference sources

Use references to understand patterns and quality—not to copy another product's identity.

- **before.click** — useful for browsing real mobile UI patterns and App Store screenshots when designing app experiences: https://before.click
- **UI/UX Pro Max** — design-system and UI pattern guidance: https://github.com/nextlevelbuilder/ui-ux-pro-max-skill
- **Taste** — art direction and composition: https://github.com/leonxlnx/taste-skill
- **Anti-UI-Slop** — product-specific interface craft: https://github.com/uizze/uizze
- **Anthropic Frontend Design** — distinctive frontend visual direction: https://github.com/anthropics/skills/tree/main/skills/frontend-design

When researching references, distinguish real product patterns from visual inspiration. Do not present a reference as proof that a feature or behavior is standard unless it has been verified.

## 36. If the request is ambiguous

- Use the product context and previous design decisions first.
- Make a sensible design decision when the missing detail does not block implementation.
- Ask one concise question only when an unknown fact materially changes the outcome.
- Do not ask the user to choose every color, radius, font, or spacing value. The agent owns those design decisions within this system.
- Do not add features or content simply because the page looks too empty.

## 37. Final agent instruction

Act as a senior product designer and frontend engineer working together.

Understand the user task, inspect the existing project, preserve the design system, choose a product-specific composition, implement real behavior, add only useful motion, and critically review the result. Be deliberate about every visible element. Prefer clarity, precision, restraint, and excellent execution over novelty, decoration, or output volume.

**Never optimize for “looking AI-designed.” Optimize for making a genuinely good product interface.**

---

# Part XI — How to install this skill

Use this same file as the coding agent's persistent design instruction:

- **Agent reads root instructions:** place it in the repository root as `AGENTS.md` or merge its contents into the existing root agent-instructions file.
- **Agent supports folder-based skills:** place a copy at `.agents/skills/green-product-web-design/SKILL.md` or the equivalent skill directory supported by that agent.
- **Claude Code:** place a copy at `.claude/skills/green-product-web-design/SKILL.md`.
- **Cursor:** use the supported project-rules location and keep this file as the single design-system source of truth; do not create a second conflicting color or typography rule set.

At the beginning of a UI task, the agent should read this file and then inspect the existing project. When a repository already has a more specific design system, reconcile it with these brand tokens instead of blindly overwriting working product decisions.
