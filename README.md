# The World of Hawas landing page

Responsive Rasasi / Hawas landing page converted to plain PHP, with custom CSS and vanilla JavaScript.

## Run it

Run it with PHP's built-in server:

```bash
php -S localhost:8080
```

Then open `http://localhost:8080`.

## Included behavior

- Responsive desktop and mobile layout
- Lead form that posts to `submit-lead.php`
- Consent-controlled WhatsApp contact card
- FAQ accordion
- Booth map button
- Accessible labels, focus styles, and reduced-motion support

Submitted leads are stored in `data/leads.jsonl`.

## Zoho CRM setup

Copy `zoho-config.example.php` to `zoho-config.php` and fill in:

- Zoho account/API domains for your data center
- Client ID
- Client Secret
- Refresh Token
- CRM module, usually `Leads`
- Field API names for custom fields such as `Type_of_Service`

The lead form always saves a local backup in `data/leads.jsonl`. If Zoho fails, the visitor still sees a success message and the error is logged in `data/zoho-errors.log`.

## Production setup

Update `WHATSAPP_NUMBER` near the top of `script.js` with the verified production WhatsApp number. Replace placeholder social and legal links with their final URLs if needed.

Tailwind is loaded through the Tailwind CDN, so no build step is required. The detailed visual matching is in `styles.css` and remains usable even before adding a Tailwind build pipeline.
