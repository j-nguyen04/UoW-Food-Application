# UoW Food Takeaway

## Installation & Setup

### Prerequisites

- XAMPP or WAMP server installed
- A web browser
- MySQL / MariaDB available via phpMyAdmin

### Steps

1. Clone the repository to your server's root directory.
   - git clone https://github.com/your-username/your-repository.git "C:\xampp\htdocs\UoW Food Takeaway"


2. Start servers:
   - Open XAMPP Control Panel and start **Apache** and **MySQL**.

3. Setup database:
   - Go to `http://localhost/phpmyadmin`
   - Create a new database named `solirestaurant`
   - Click **Import** and select the `solirestaurant.sql` file from the project folder

4. Configure:
   - Ensure `config.php` has the correct database credentials
   - Default XAMPP credentials are usually:
     - user: `root`
     - password: (empty)

5. Run:
   - Open your browser and visit:
     `http://localhost/UoW%20Food%20Takeaway/`

## Stripe Setup

This project requires Stripe API keys for checkout functionality.

### Required environment variables

- `STRIPE_SECRET_KEY` — Stripe test secret key (`sk_test_...`)
- `STRIPE_PUBLISHABLE_KEY` — Stripe test publishable key (`pk_test_...`)
- `APP_URL` — the base application URL, such as `http://localhost/UoW-Food-Takeaway`
- `STRIPE_CURRENCY` — optional, defaults to `gbp`

### Stripe key instructions

1. Sign in to your Stripe dashboard at https://dashboard.stripe.com
2. Switch to **Test mode**
3. Create or copy the test secret key and publishable key
4. Set those values in your local environment before running the app

### Example local setup (PowerShell)

```powershell
$env:STRIPE_SECRET_KEY = "sk_test_..."
$env:STRIPE_PUBLISHABLE_KEY = "pk_test_..."
$env:APP_URL = "http://localhost/UoW-Food-Takeaway"
```

In XAMPP/Apache, you can also configure these in your virtual host or system environment.

### Notes

The project falls back to placeholder values only when environment variables are not set, so real Stripe keys must be provided for payment processing to work.
