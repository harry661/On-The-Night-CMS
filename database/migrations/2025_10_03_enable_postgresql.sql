-- Migration for PostgreSQL support on Railway
-- This replaces SQLite with PostgreSQL for production deployment

-- Update venues table for PostgreSQL
ALTER TABLE venues ALTER COLUMN latitude TYPE DECIMAL(8,6);
ALTER TABLE venues ALTER COLUMN longitude TYPE DECIMAL(8,6);

-- Update deals table for PostgreSQL  
ALTER TABLE deals ALTER COLUMN ticket_price TYPE DECIMAL(8,2);

-- Update events table for PostgreSQL
ALTER TABLE events ALTER COLUMN ticket_price TYPE DECIMAL(8,2);
