<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../Logs/error.log');

require_once __DIR__. "/Database.php";

class Student {
    
    private PDO $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    //____________________STUDENT-SPECIFIC METHODS____________________

    //Get Courses for Student
    public function getCoursesForStudent(int $student_id): array {
        $stmt = $this->conn->prepare("SELECT courses.*, users.first, users.last FROM courses JOIN advisor_students ON 
        courses.advisor_id =advisor_students.advisor_id JOIN users ON courses.advisor_id = users.id WHERE advisor_students.student_id = ?
        ORDER BY courses.course_title ASC ");
        $stmt->execute([$student_id]);
        return $stmt->fetchAll();
    }

    //Students to enroll courses
    public function enrollCourse(int $student_id, int $course_id): array {
        try {
            //Checked if already enrolled
            $check = $this->conn->prepare("SELECT id FROM student_courses WHERE student_id = ? AND course_id = ?");
            $check->execute([$student_id, $course_id]);

            if($check->fetch()) {
                return ['success' => false, 'error' => 'You are already enrolled in this course.'];
            }

            //Enroll student
            $stmt = $this->conn->prepare("INSERT INTO student_courses (student_id, course_id) VALUES (?, ?)");
            $stmt->execute([$student_id, $course_id]);
            return ['success' => true];
        } catch (PDOException $e) {
            error_log("enrollCourse error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to enroll in course. Please try again later.'];
        }
    }

    //Get course a student is enrolled in
    public function getEnrolledCourses(int $student_id): array {
        try {
            $stmt = $this->conn->prepare("SELECT courses.* FROM courses JOIN student_courses ON courses.id = student_courses.course_id WHERE 
            student_courses.student_id = ? ORDER BY courses.course_title ASC ");
            $stmt->execute([$student_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("getEnrolledCourses error: " .$e->getMessage());
            return [];
        }
    }

    //Student to drop courses
    public function dropCourse(int $student_id, int $course_id): array {
        try {
            $stmt = $this->conn->prepare("DELETE FROM student_courses WHERE student_id = ? AND course_id = ? ");
            $stmt->execute([$student_id, $course_id]);
            return ['success' => true];
        } catch (PDOException $e) {
            error_log ("dropCourse error: " .$e->getMessage());
            return['success' => false, 'error' => 'Could not drop course. Please try again.'];
        }
    }

    //Submit Assignment for Student
    public function submitAssignment(int $student_id, int $course_id, string $filename): array {
        try {
                
            //Check if student enrolled in the course 
            $check = $this->conn->prepare("SELECT id FROM student_courses WHERE student_id = ? AND course_id = ?");
            $check->execute([$student_id, $course_id]);

            if(!$check->fetch()) {
                return ['success' => false, 'error' => 'You are not enrolled in this course.'];
            }

            //Insert into assignments table
            $stmt = $this->conn->prepare("INSERT INTO assignments (student_id, course_id, filename) VALUES (?, ?, ?) ");
            $stmt->execute([$student_id, $course_id, $filename]);
            return ['success' => true];
        } catch (PDOException $e) {
            error_log("submitAssignment error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Could not submit assignment. Please try again later.'];
        }
    }

    //Get all assignment submission for a student
    public function getStudentSubmissions(int $student_id): array {
        try {
            $stmt = $this->conn->prepare("SELECT assignments.id, assignments.filename, assignments.submitted_at, courses.course_title, FROM 
            assignments JOIN courses ON assignments.course_id = courses.id WHERE assignments.student_id = ? 
            ORDER BY assignments.submitted_at DESC");
            $stmt->execute([$student_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("getStudentSubmissions error: " .$e->getMessage());
            return [];
        }
    }

    //Check Results for Student
    public function canViewResults(): bool {
        try {
            $stmt = $this->conn->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'results_released' ");
            $stmt->execute();
            return $stmt->fetchColumn() === '1';
        } catch (PDOException $e) {
            error_log("canViewResults error: " . $e->getMessage());
            return false;
        }
    }

    //Get Results for Student
    public function getResultsForStudent(int $student_id): array {
        try {
            $stmt = $this->conn->prepare("SELECT results.*, courses.course_title FROM results JOIN courses ON results.course_id = courses.id
            WHERE results.student_id = ? ORDER BY courses.course_title ASC");
            $stmt->execute([$student_id]);
            return $stmt->fetchAll();
        }catch(PDOException $e) {
            error_log("getResultsForStudent error: " . $e->getMessage());
            return [];
        }
    }

    //Get Results for Student (for Print)
    public function getStudentResults(int $student_id): array {
        try {
            $stmt = $this->conn->prepare("SELECT results.*, courses.course_title, courses.course_code FROM results JOIN courses ON 
            results.course_id = courses.id WHERE results.student_id = ? ORDER BY courses.course_title ASC");
            $stmt->execute([$student_id]);
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("getStudentResults error: " . $e->getMessage());
            return [];
        }
    }

}

?>