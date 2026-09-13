CREATE TABLE IF NOT EXISTS categories (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  type TEXT NOT NULL CHECK(type IN ('expense','income')),
  created_at TEXT,
  updated_at TEXT
);

CREATE TABLE IF NOT EXISTS suppliers (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  created_at TEXT,
  updated_at TEXT
);

CREATE TABLE IF NOT EXISTS expenses (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  date TEXT NOT NULL,
  category_id INTEGER NOT NULL,
  amount REAL NOT NULL,
  description TEXT,
  proof_type TEXT NOT NULL DEFAULT 'none',
  supplier_id INTEGER,
  vat_amount REAL NOT NULL DEFAULT 0,
  created_at TEXT,
  updated_at TEXT,
  FOREIGN KEY(category_id) REFERENCES categories(id),
  FOREIGN KEY(supplier_id) REFERENCES suppliers(id)
);

CREATE TABLE IF NOT EXISTS incomes (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  date TEXT NOT NULL,
  category_id INTEGER NOT NULL,
  amount REAL NOT NULL,
  description TEXT,
  created_at TEXT,
  updated_at TEXT,
  FOREIGN KEY(category_id) REFERENCES categories(id)
);
