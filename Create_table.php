<?php
require_once __DIR__. "/classes/Database.php";
$conn = Database::getConnection();

//Users table
try {
    $conn->exec("CREATE TABLE IF NOT EXISTS users (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    first           VARCHAR(100)    NOT NULL,
    last            VARCHAR(100)    NOT NULL,
    email           VARCHAR(255)    NOT NULL UNIQUE,
    password        VARCHAR(255)    NOT NULL,
    role            ENUM('admin', 'advisor', 'student') NOT NULL Default 'student',
    profile_image   VARCHAR(255)    DEFAULT NULL,
    created_at      TIMESTAMP       DEFAULT CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {
    echo "Error in creating users table: " .$e->getMessage(); 
}

//Advisor-Student relationship table
try {
    $conn->exec("CREATE TABLE IF NOT EXISTS advisor_students (
    id              INT AUTO_INCREMENT      PRIMARY KEY,
    advisor_id      INT                     NOT NULL,
    student_id      INT                     NOT NULL,
    assigned_at     TIMESTAMP               DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY     (advisor_id)            REFERENCES users(id)    ON DELETE CASCADE,
    FOREIGN KEY     (student_id)            REFERENCES users(id)    ON DELETE CASCADE,
    UNIQUE          (advisor_id, student_id)
    )");
} catch (PDOException $e) {
    echo "Error in creating advisor_students table: " .$e->getMessage();
}

//Courses Table 
try {
    $conn->exec("CREATE TABLE IF NOT EXISTS courses (
    id              INT AUTO_INCREMENT  PRIMARY KEY,
    user_id         INT             NOT NULL,
    course_title    VARCHAR(255)        NOT NULL,
    course_code     VARCHAR(255)        NOT NULL,
    description     TEXT,
    created_at      TIMESTAMP           DEFAULT CURRENT_TIMESTAMP, 
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
} catch (PDOException $e) {
    echo "Error in creating courses table: " .$e->getMessage();
}

//Student Register Courses table
try {
    $conn->exec("CREATE TABLE IF NOT EXISTS student_courses (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    student_id      INT            NOT NULL,   
    course_id       INT            NOT NULL,
    enrolled_at      TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY     (student_id)   REFERENCES users(id)    ON DELETE CASCADE,
    FOREIGN KEY     (course_id)    REFERENCES courses(id)  ON DELETE CASCADE,
    UNIQUE          (student_id, course_id)

    )");
} catch (PDOException $e) {
    echo "Error in creating student_courses table: " .$e->getMessage();
}

//Assignments table
try {
    $conn->exec("CREATE TABLE IF NOT EXISTS assignments (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    student_id          INT NOT NULL,
    course_id           INT NOT NULL,
    filename            VARCHAR(255) NOT NULL,
    submitted_at        TIMESTAMP   DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY         (student_id)   REFERENCES users(id),
    FOREIGN KEY         (course_id)    REFERENCES courses(id)
    )");
} catch (PDOException $e) {
    echo "Error in creating assignment table: " .$e->getMessage(); 
}

//Grade Table
try {
    $conn->exec("CREATE TABLE IF NOT EXISTS grades (
    id                  INT AUTO_INCREMENT  PRIMARY KEY,
    assignment_id       INT NOT NULL,
    student_id          INT NOT NULL,
    course_id           INT NOT NULL,
    score               VARCHAR(100),
    comment             TEXT,
    graded_at           TIMESTAMP   DEFAULT CURRENT_TIMESTAMP,
    is_released          BOOLEAN     DEFAULT FALSE,
    FOREIGN KEY         (assignment_id)     REFERENCES assignments(id),
    FOREIGN KEY         (student_id)        REFERENCES  users(id),
    FOREIGN KEY         (course_id)         REFERENCES  courses(id)
    )");
} catch (PDOException $e) {
    echo "Error in creating grade table: " .$e->getMessage();
}

//Release Grades result on Student Dashboard
try {
    $conn->exec("CREATE TABLE system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value VARCHAR(255) NOT NULL
)");
} catch (PDOException $e) {
    echo "Error in creating system_settings table: " .$e->getMessage();
}   

?>
