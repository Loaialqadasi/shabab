-- USERS
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- LOST ITEMS
CREATE TABLE lost_items (
    id SERIAL PRIMARY KEY,
    item_name VARCHAR(100) NOT NULL,
    description TEXT,
    date_lost DATE,
    location VARCHAR(100),
    image VARCHAR(255),
    reported_by VARCHAR(50)
);

-- FOUND ITEMS
CREATE TABLE found_items (
    id SERIAL PRIMARY KEY,
    item_name VARCHAR(100) NOT NULL,
    description TEXT,
    date_found DATE,
    location VARCHAR(100),
    image VARCHAR(255),
    reported_by VARCHAR(50)
);

-- CHAT MESSAGES
CREATE TABLE chat_messages (
    id SERIAL PRIMARY KEY,
    sender VARCHAR(20) NOT NULL, -- 'user' or 'bot'
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
