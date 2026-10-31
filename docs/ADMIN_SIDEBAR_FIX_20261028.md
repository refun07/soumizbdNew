# Admin sidebar flyout correction

Deployed to `https://soumisbangladesh.com/admin/orders` on 28 October 2026, Bangladesh time.

The collapsed desktop sidebar treated every `.expanded` group as a visible flyout. Orders is expanded on its active route, and Products is always expanded for users with product access. Both menus therefore remained over the Orders content without hover or keyboard focus.

The desktop flyout styles now ignore the accordion's persistent expanded state. Hover or visible keyboard focus reveals the relevant flyout; moving away hides it. Pointer focus alone cannot pin it open, and a pointer bridge covers the gutter between an icon and its menu. The expanded desktop accordion behavior is retained.

Responsive verification also found the desktop `in-active` class and inline 90px width carrying into the mobile drawer. The shared navigation initializer now removes both when entering the mobile breakpoint, allowing inline accordion lists and the normal drawer width.

## Verification

- Production Vite build passed. Only the navigation sources, its two compiled assets, and the admin main entry in the existing live manifest were published. Other manifest entries were retained.
- On the deployed Orders page, Orders and Products flyouts are hidden at rest in collapsed mode.
- Products opens on hover, stays open across the gutter, and closes after pointer exit.
- Keyboard Tab focus opens Products. Expanded desktop navigation remains 300px with inline lists.
- At 375px, the live drawer opens at 300px, has no compact desktop class, uses inline lists, and closes with Escape.
- Temporary viewport/style test overrides were reset by clearing emulation and reloading. Orders was left open with the normal desktop navigation visible.
- Both guarded private deployment checks completed and the temporary cron job was removed. cPanel confirms No Cron Jobs. No order or database records changed.

Final assets: `main-40d87e82.js` and `main-c44a50ee.css`.

Private rollback backups, in deployment order:

1. `/home/raisamar/shomiz-deploy-data/admin-sidebar-20261028/before-update.zip`
2. `/home/raisamar/shomiz-deploy-data/admin-sidebar-gutter-20261028/before-update.zip`

Local evidence, deployment records, and browser results: `artifacts/admin-sidebar-20261028/`.

## Follow-up: mixed pointer and keyboard input

The user reported another overlap after the initial release. It was reproduced with Products retaining visible keyboard focus while the pointer hovered Orders: each independent CSS selector exposed its own group. The follow-up stylesheet gives the pointer-selected group priority over a previously keyboard-focused group, preserving keyboard navigation when no group is hovered.

Final live verification on Orders, without injected test styles:

- Products keyboard focus + Orders hover: only Orders visible.
- Orders keyboard focus + Products hover: only Products visible.
- Clicking the Orders Panel heading outside the navigation: zero visible flyouts.

The new stylesheet is `main-c68505bb.css`; the JavaScript stays `main-40d87e82.js`. The companion CSS manifest entry was also synchronized. No order data changed. The temporary deployment schedule was removed and cPanel confirms No Cron Jobs. The additional private rollback backup is `/home/raisamar/shomiz-deploy-data/admin-sidebar-exclusive-20261028/before-update.zip`. Evidence is in `artifacts/admin-sidebar-20261028/exclusive/`.
