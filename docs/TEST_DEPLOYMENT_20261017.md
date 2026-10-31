# Test deployment — 17 October 2026

- URL: https://test.soumisbangladesh.com
- cPanel account: raisamar
- Public folder: /home/raisamar/test.soumisbangladesh.com
- Private Laravel folder: /home/raisamar/soumis-test-app
- Database/user: raisamar_test (password stored only in server .env)
- PHP: ea-php82, assigned only to test subdomain.
- Release SHA-256: 3b163972fd163ec0726f0b3453f60cbd99809c2876f778fc5914f99a0c072053
- Hosting configuration backup: /home/raisamar/backupfolder/shomiz-test-before-20261017.zip

The guarded CLI installer ran through a temporary cPanel cron job. It checked PHP/extensions, verified the archive hash, required an empty target database, backed up the original hosting files, extracted Laravel privately, copied public assets, imported the database, and rebuilt config/views. The cron job was removed after completion. Temporary installer/support archive were moved to cPanel Trash; the deployment package and completion marker remain outside the public folder.

The catalog/settings were transferred with database schema and non-customer administrative users. Temporary orders, order items/groups, transactions, carts, checkout sessions, customer accounts/addresses, login tokens, and support fixtures were excluded. The local application and database were not changed.

Verified over HTTPS: homepage/catalog images, Health Care category and two products, admin login and homepage content editor, mobile cart and checkout at 375px without page overflow, add/remove cart item, inside-Dhaka shipping ৳70 and total ৳570 for a ৳500 item. No order or payment was submitted. Test domain has `noindex, follow`; final public domain indexing must be enabled at launch. SMTP remains the existing log driver; external mail/OTP/payment delivery was not verified.
