# House Budjet

Bookkeeping app with Dashboard, Income, Expenses, and Report.

Default expense categories: **Faustina**, **Fiona**.

The expenses table includes `proof_type` so Add Expense can save receipts/invoices.

## Run

Keep any existing bookkeeping server running. Start House Budjet on a second port:

```bash
php -S 0.0.0.0:8000 -t public router.php
```

Open http://127.0.0.1:8000

## Local Laravel API fix

If you already run the Laravel API at `api/database/database.sqlite` and Add Expense fails with `table expenses has no column named proof_type`, copy:

`api/database/migrations/2026_09_13_131326_add_proof_type_to_expenses_table.php`

into that Laravel project and run:

```bash
php artisan migrate
```

Or patch SQLite directly:

```bash
sqlite3 api/database/database.sqlite "ALTER TABLE expenses ADD COLUMN proof_type TEXT NOT NULL DEFAULT 'none';"
```
