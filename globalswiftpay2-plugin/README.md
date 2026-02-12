# GlobalSwiftPay2 WordPress Plugin

A professional investment website plugin for WordPress with dark mode, glassmorphic UI, and comprehensive payment features.

## Features

### Design & UI
- **Dark Mode Toggle**: Switch between dark and light modes with smooth transitions
- **Glassmorphic Effects**: Modern glass-like UI elements throughout the site
- **Floating Navigation**: Pill-style glassmorphic header menu
- **Mobile Responsive**: Sleek hamburger menu with side panel for mobile devices
- **Iconify Integration**: Solar linear icons throughout the site
- **Beam Animations**: Subtle animated effects on various elements
- **Scroll Progress**: Animated scroll-to-top button with progress indicator

### Pages & Sections
- **Hero Section**: Grid background with animated circles, noodles, and CTAs
- **Trust Section**: Instant, Versatile, Global, Secure features
- **How It Works**: Upgrade → Convert → Save workflow
- **Pay Online**: Laptop graphic with cryptocurrency icons
- **Transfer Funds**: Peer-to-peer transfer visualization
- **Cryptocurrency Table**: Real-time prices from CoinGecko API
- **Contact Form**: AJAX-powered form with email notifications
- **About Page**: Company history, mission, and stats
- **Generate Page**: Security phrase generation with BTC payment
- **Security Policy**: Fraud protection features
- **Privacy Policy**: Data handling information
- **Terms & Conditions**: Service terms

### Admin Panel
- **General Settings**: BTC address, support email, default theme
- **Page Links**: Configure upgrade, convert, save, dashboard links
- **Images**: Upload hero, laptop, about, and other page images
- **Payment Proofs**: View uploaded payment proof files

## Installation

1. Upload the `globalswiftpay2-plugin` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure settings under 'GSP2 Settings' in the admin menu
4. Flush permalinks (Settings → Permalinks → Save Changes)

## Shortcodes

- `[gsp2_homepage]` - Full homepage layout
- `[gsp2_hero]` - Hero section only
- `[gsp2_trust]` - Trust building section
- `[gsp2_how_it_works]` - How it works section
- `[gsp2_pay_online]` - Pay online section
- `[gsp2_transfer]` - Transfer funds section
- `[gsp2_crypto_table]` - Cryptocurrency prices table
- `[gsp2_contact]` - Contact form
- `[gsp2_footer]` - Footer section
- `[gsp2_navigation]` - Navigation bar
- `[gsp2_dark_mode_toggle]` - Dark mode toggle button
- `[gsp2_forgot_password]` - Forgot password form

## Custom Pages

The plugin creates the following custom pages:
- `/gsp2-about/` - About Us page
- `/gsp2-generate/` - Generate Security Phrase page
- `/gsp2-security-policy/` - Security Policy page
- `/gsp2-privacy-policy/` - Privacy Policy page
- `/gsp2-terms/` - Terms and Conditions page
- `/gsp2-forgot-password/` - Forgot Password page

## Configuration

### Admin Settings
Navigate to **GSP2 Settings** in your WordPress admin panel:

1. **General**: Set BTC payment address, support email, and default theme
2. **Page Links**: Configure internal links for dashboard, upgrade, convert, etc.
3. **Images**: Upload custom images for various sections

### Default BTC Address
`bc1qf74tnfccynx78n9kd8cgjcqp8l9s7y5fcre2hp`

### Default Support Email
`support@globalswiftpay2.com`

## License

GPL v2 or later

## Credits

- Icons: [Iconify](https://iconify.design/) - Solar Linear Icons
- Crypto Data: [CoinGecko API](https://www.coingecko.com/en/api)
- Fonts: Inter (System Font Stack)

## Version

1.0.0

## Author

Global Swift Pay - https://globalswiftpay2.com
