CREATE DATABASE IF NOT EXISTS test_system CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE test_system;

CREATE TABLE tests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    test_id INT,
    question TEXT,
    type ENUM('open', 'closed'),
    answers TEXT, -- CSV за затворени
    correct_answer TEXT,
    FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE
);

CREATE TABLE results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    test_id INT,
    user VARCHAR(100),
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    result_id INT,
    question_id INT,
    answer TEXT,
    FOREIGN KEY (result_id) REFERENCES results(id),
    FOREIGN KEY (question_id) REFERENCES questions(id)
);

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    result_id INT,
    reviewer VARCHAR(100),
    review_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE review_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    review_id INT,
    question_id INT,
    is_correct BOOLEAN,
    comment TEXT,
    FOREIGN KEY (review_id) REFERENCES reviews(id),
    FOREIGN KEY (question_id) REFERENCES questions(id)
);
