# The World of Hawas landing page

Responsive recreation of the supplied Rasasi / Hawas landing-page design using HTML, Tailwind CSS, custom CSS, and vanilla JavaScript.

## Run it

Open `index.html` directly in a browser, or run a local server:

```bash
python3 -m http.server 8080
```

Then open `http://localhost:8080`.

## Included behavior

- Responsive desktop and mobile layout
- Lead form that creates a pre-filled WhatsApp enquiry
- Consent-controlled WhatsApp contact card
- FAQ accordion
- Booth map button
- Accessible labels, focus styles, and reduced-motion support

## Production setup

Update `WHATSAPP_NUMBER` near the top of `script.js` with the verified production WhatsApp number. Replace placeholder social and legal links with their final URLs if needed.

Tailwind is loaded through the Tailwind CDN, so no build step is required. The detailed visual matching is in `styles.css` and remains usable even before adding a Tailwind build pipeline.
