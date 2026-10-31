# Unified homepage banner

Replaced the separated hero copy/photo treatment with one rounded mint banner canvas. Desktop artwork blends into the background behind editable copy. Mobile copy and artwork remain inside a single continuous frame; buttons wrap at narrow widths. Existing image, text, labels and links remain driven by the admin homepage settings.

Verified locally at 320, 375, 768, 1024 and 1440px: copy contained and no horizontal overflow, with 48px or greater CTA heights. Live test site verified at 375 and 1440px with loaded artwork and no overflow. Production build and git diff --check passed. Deployed shomiz-hero-banner-20261017.zip through authenticated cPanel, preserving existing release archives. Screenshots and measurements are in artifacts/hero-banner/.
