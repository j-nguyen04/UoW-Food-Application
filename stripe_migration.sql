ALTER TABLE orders
ADD COLUMN IF NOT EXISTS payment_status VARCHAR(50) DEFAULT 'unpaid' AFTER user_id,
ADD COLUMN IF NOT EXISTS payment_method VARCHAR(50) DEFAULT NULL AFTER payment_status,
ADD COLUMN IF NOT EXISTS payment_amount DECIMAL(10,2) DEFAULT NULL AFTER payment_method,
ADD COLUMN IF NOT EXISTS payment_currency CHAR(3) DEFAULT 'GBP' AFTER payment_amount,
ADD COLUMN IF NOT EXISTS stripe_checkout_session_id VARCHAR(255) DEFAULT NULL AFTER payment_currency,
ADD COLUMN IF NOT EXISTS stripe_payment_intent_id VARCHAR(255) DEFAULT NULL AFTER stripe_checkout_session_id;

ALTER TABLE orders
ADD UNIQUE KEY IF NOT EXISTS unique_stripe_checkout_session_id (stripe_checkout_session_id);

CREATE TABLE IF NOT EXISTS payment_sessions (
    stripe_checkout_session_id VARCHAR(255) PRIMARY KEY,
    user_id INT NOT NULL,
    items_json LONGTEXT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    currency CHAR(3) NOT NULL DEFAULT 'GBP',
    processed_order_id CHAR(4) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    processed_at DATETIME DEFAULT NULL,
    CONSTRAINT payment_sessions_user_fk FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
