CREATE TABLE magi_chat_sessions (
    session_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NULL,
    status VARCHAR(50) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE magi_chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    sender VARCHAR(20),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE magi_uploaded_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    uploaded_by VARCHAR(50),
    file_name VARCHAR(255),
    file_path VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
