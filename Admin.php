<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../Logs/error.log');

require_once __DIR__. "/Database.php";

class Admin {

    private PDO $conn;

    public function __construct() {
        $this->conn = Database::getConnection();            
    }

    //__________USER MANAGEMENT________________

    //Get every user in the system
    public function getAllUsers(): array {
        try {
            $stmt = $this->conn->prepare("SELECT id, first, last, email, role, created_at FROM users ORDER BY created_at DESC");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("getAllUsers error:" .$e->getMessage());
            return [];
        }
    }

    //Get one user by ID for the details/edit page
    public function getUserById(int $id): array|false {
        try {
            $stmt = $this->conn->prepare("SELECT id, first, last, email, role, profile_image, created_at FROM users where id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("getUserById error: " .$e->getMessage());
            return false;
        }
    }

    //Search users by name or email
    public function searchUsers(string $keyword): array {
        try {
            $like = '%' .$keyword .'%';
            $stmt = $this->conn->prepare("SELECT id, first, last, email, role, created_at FROM users
            WHERE first LIKE ? OR last LIKE ? OR email LIKE ? ORDER BY created_at DESC");
            $stmt->execute([$like, $like, $like]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("searchUsers error:" .$e->getMessage());
            return [];
        }
    }

    //Filter user by role - 'student', 'advisor', 'admin'
    public function getUsersByRole(string $role): array {
        try{
            $stmt = $this->conn->prepare("SELECT id, first, last, email, role, created_at FROM users where role = ? ORDER BY created_at DESC");
            $stmt->execute([$role]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("getUserByRole error: " .$e->getMessage());
            return [];
        }
    }

    //Change a user role e.g student to advisor
    public function updateUserRole(int $id, string $role): array {
        $allowed = ['student', 'advisor', 'admin'];
        if(!in_array($role, $allowed)) {
            return ['success' => false, 'error' => 'Invalid role selected.'];
        }
        try {
            $stmt = $this->conn->prepare("UPDATE users SET role = ? WHERE id = ? ");
            $stmt->execute([$role, $id]);
            return ['success' => true];
        } catch(PDOException $e) {
            error_log("updateUserRole error: " .$e->getMessage());
            return ['success' => false, 'error' => 'Could not update role. Please try again.'];
        }
    }

    //Delete users account completely
    public function deleteUser(int $id): array {
        try {
            $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return ['success' => true];
        } catch (PDOException $e) {
            error_log("deleteUser error: " .$e->getMessage());
            return ['success' => false, 'error' => 'Could not delete user. Please try again.'];
        }
    }

    //___________ADVISOR MANAGEMENT________________

    //Create a new advisor account directly
    public function createAdvisor(string $first, string $last, string $email, string $password): array {
        if (empty($first) || empty($last) || empty($email) || empty($password)) {
            return ['success' => false, 'error'=> 'All fields are required.'];
        }
        if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error'=> 'Please enter a valid email address.'];
        }
        if(!empty($password) && strlen($password) < 6) {
            return ['success' => false, 'error' => 'Password must be at least six characters.'];
        }
        try {
            //Check if email already exists
            $check = $this->conn->prepare("SELECT id FROM users WHERE email = ? ");
            $check->execute([$email]);
            if ($check->fetch()) {
                return ['success' => false, 'error' => 'An account with that email already exists'];
            }
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("INSERT INTO users(first, last, email, password, role) VALUES (?, ?, ?, ?, 'advisor')");
            $stmt->execute([$first, $last, $email, $hash]);
            return ['success' => true];
        } catch (PDOException $e) {
            error_log("createAdvisor error: " .$e->getMessage());
            return ['success' => false, 'error' => 'Could not create advisor. Please try again.'];

        }
    }

    //Assign Student to Advisor
    public function assignStudentToAdvisor(int $student_id, int $advisor_id): array {
        try {
            //Verify Student role
            $checkStudent = $this->conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'student' ");
            $checkStudent->execute([$student_id]);

            if(!$checkStudent->fetch()) {
                return ['success' => false, 'error' => 'Selected user is not a student.'];
            }

            //Verify Advisor role
            $checkAdvisor = $this->conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'advisor' ");
            $checkAdvisor->execute([$advisor_id]);

            if(!$checkAdvisor->fetch()) {
                return ['success' => false, 'error' => 'Selected user is not an advisor.'];
            }

            //Remove existing assignment (one student to one advisor)
            $del = $this->conn->prepare("DELETE FROM advisor_students WHERE student_id = ?");
            $del->execute([$student_id]);

            //Insert new assignments
            $stmt = $this->conn->prepare("INSERT INTO advisor_students(advisor_id, student_id) VALUES (?, ?) ");
            $stmt->execute([$advisor_id, $student_id]);
            return ['success' => true];
        } catch (PDOException $e) {
            error_log("assignStudentToAdvisor error: " .$e->getMessage());
            return ['success' => false, 'error' => 'Could not assign advisor. Please try again.'];
        }
    }

    //Remove a student's advisor
    public function unassignStudent(int $student_id): array {
        try {
            $stmt = $this->conn->prepare("DELETE FROM advisor_students WHERE student_id = ?");
            $stmt->execute([$student_id]);
            return ['success' => true];
        } catch (PDOException $e) {
            error_log("unassignStudent error: " .$e->getMessage());
            return ['success' => false, 'error' => 'Could not unassign advisor. Please try again.'];
        }
    }

    //Get all students with their assigned advisor
    public function getAllStudentsWithAdvisors(): array {
        try {
            $stmt = $this->conn->prepare("SELECT users.id, users.first, users.last, users.email, users.profile_image, users.created_at,
            advisor_students.advisor_id, adv.first AS advisor_first, adv.last AS advisor_last, adv.email AS advisor_email FROM users
            LEFT JOIN advisor_students ON users.id = advisor_students.student_id 
            LEFT JOIN users AS adv ON advisor_students.advisor_id = adv.id WHERE users.role = 'student' ORDER BY users.created_at DESC");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("getAllStudentsWithAdvisors error: " .$e->getMessage());
            return [];
        }
    }

    //Get total unassigned students
    public function getTotalUnassignedStudents(): int {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) FROM users LEFT JOIN advisor_students ON users.id = advisor_students.student_id 
            WHERE role = 'student' AND advisor_students.student_id IS NULL");
            return $stmt->fetchColumn();
        } catch (PDOException $e){
            error_log("getTotalUnassignedStudents error: " .$e->getMessage());
            return 0;
        }
    }

    //Get all advisors that are taking those courses
    public function getAllAdvisors(): array {
        try {
            $stmt = $this->conn->prepare("SELECT id, first, last, email FROM users WHERE role = 'advisor' ORDER BY first ASC");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("getAllAdvisors error: " .$e->getMessage());
            return[];
        }
    }

    //Assign Advisor to each course
    public function assignCourseToAdvisor(int $course_id, int $advisor_id): array {
        try {
            //Verify if course exist
            $verifyCourse = $this->conn->prepare("SELECT id FROM courses WHERE id = ? ");
            $verifyCourse->execute([$course_id]);

            if(!$verifyCourse->fetch()) {
                return ['success' => false, 'error' => 'Course could not be found'];
            }

            //Verify if advisor exist
            $verifyAdvisor = $this->conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'advisor' ");
            $verifyAdvisor->execute([$advisor_id]);

            if(!$verifyAdvisor->fetch()) {
                return ['success' => false, 'error' => 'Invalid advisor selected'];
            }

            //Assign course
            $stmt = $this->conn->prepare("UPDATE courses SET advisor_id = ? WHERE id = ?");
            $stmt->execute([$advisor_id, $course_id]);
            return ['success' => true];
        } catch (PDOException $e) {
            error_log("assignCourseToAdvisor error: " .$e->getMessage());
            return ['success' => false, 'error' => "Could not assign course to advisor"];
        }
    }

    //Unassign course to advisor
    public function unassignCourse(int $course_id): array {
        try {
            $stmt = $this->conn->prepare("UPDATE courses SET advisor_id = NULL WHERE id = ?");
            $stmt->execute([$course_id]);
            return ['success' => true];
        } catch (PDOException $e) {
            error_log("unassignCourse error: " .$e->getMessage());
            return ['success' => false, 'error' => 'Could not unassign advisor. Please try again.'];
        }
    }
    

    //____________COURSE MANAGEMENT_______________

    //Create a new course and assign to an advisor  
    public function createCourse(string $course_title, string $course_code, string $description): array {
        //Validate inputs
        if (empty(trim($course_title)) || empty(trim($course_code))) {
            return ['success' => false, 'error' => 'Course title and code are required.'];
        }
        try {
            //Check if course exist
            $existing = $this->conn->prepare("SELECT id FROM courses WHERE course_code = ? ");
            $existing->execute([$course_code]);

            if($existing->fetch()) {
                return ['success' => false, 'error' => 'Course code already exists.'];
            }

            //Create course
            $stmt = $this->conn->prepare("INSERT INTO courses (course_title, course_code, description) VALUES (?, ?, ?)");
            $stmt->execute([trim($course_title), trim($course_code), trim($description)]);
            return ['success' => true];
        } catch (PDOException $e) {
            error_log("createCourse error: " .$e->getMessage());
            return ['success' => false, 'error' => 'Could not create course. Please try again.'];
        }

    }


    //Get all courses across users
    public function getAllCourses(): array {
        try {
            $stmt = $this->conn->prepare("SELECT courses.id, courses.course_title, courses.course_code, courses.description, courses.advisor_id, 
            courses.created_at, users.first AS advisor_first, users.last AS advisor_last FROM courses LEFT JOIN users ON 
            courses.advisor_id = users.id ORDER BY courses.created_at DESC");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("getAllCourses error: " .$e->getMessage());
            return [];
        }        
    }

    //Delete any course by id
    public function deleteCourse(int $id): array {
        try {
        $stmt= $this->conn->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        return ['success'=>true];
        } catch (PDOException $e) {
            error_log("deleteCourse error: " .$e->getMessage());
            return ['success' => false, 'error' => 'Could not delete course. Please try again']; 
        }        
    }

    //__________ASSIGNMENT MANAGEMENT_______________________

    //Get all assignment submission across all students
    public function getAllAssignments(): array {
        try {
            $stmt = $this->conn->prepare("SELECT assignments.id, assignments.filename, assignments.submitted_at, users.first, users.last,
            users.email, courses.course_title, courses.course_code FROM assignments JOIN users ON assignments.student_id = users.id JOIN
            courses ON assignments.course_id = courses.id ORDER BY assignments.submitted_at DESC");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
           error_log('getAssignments error: ' .$e->getMessage());
           return []; 
        }
    }

    //Delete an assignment submission
    public function deleteAssignment(int $id): array {
        try {
            $stmt = $this->conn->prepare("DELETE FROM assignments WHERE id = ? ");
            $stmt->execute([$id]);
            return ['success' => true];
        } catch (PDOException $e) {
            error_log('deleteAssignment error: ' .$e->getMessage());
            return ['success' => false, 'error' => 'Could not delete assignment. Please try again'];
        }
    }

    //____________DASHBOARD STATISTICS_______________

    //Count all registered students
    public function getTotalUsers(): int {
        try {
            return (int) $this->conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
        } catch (PDOException $e) {
            error_log('getTotalUsers' .$e->getMessage());
            return 0;
        }
    }

    //Count by role
    public function getTotalByRole(string $role): int {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM users WHERE role = ? ");
            $stmt->execute([$role]);
            return(int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('getTotalByRole error: ' .$e->getMessage());
            return 0;
        }        
    }

    //Count the total courses in the system
    public function getTotalCourses(): int {
        try {
            return (int) $this->conn->query("SELECT COUNT(*) FROM courses")->fetchColumn();
        } catch (PDOException $e) {
            error_log('getTotalCourses error: ' .$e->getMessage());
            return 0;
        }
    }

    //Count total assignment submission
    public function getTotalAssignments(): int {
        try {
            return (int) $this->conn->query("SELECT COUNT(*) FROM assignments")->fetchColumn();
        } catch (PDOException $e) {
            error_log('getTotalAssignments error: ' .$e->getMessage());
            return 0;
        }
    }

    //Get most recently registered students
    public function getRecentUsers(int $limit = 5): array {
        try {
            $stmt = $this->conn->prepare("SELECT id, first, last, email, role, created_at FROM users ORDER BY created_at DESC LIMIT ?");
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('getRecentUsers error: ' .$e->getMessage());
            return [];
        }
    }

    //Get most recent submission
    public function getRecentAssignments(int $limit = 5): array {
        try {
            $stmt = $this->conn->prepare("SELECT assignments.id, assignments.filename, assignments.submitted_at, users.first, users.last, 
            courses.course_title, courses.course_code FROM assignments JOIN users ON assignments.student_id = users.id JOIN courses ON 
            assignments.course_id = courses.id ORDER BY assignments.submitted_at DESC LIMIT ?");
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('getRecentAssignments error: ' .$e->getMessage());
            return [];
        }
    }
    
    //___________RESULTS MANAGEMENT_____________________

    //Get all students results
    public function getAllResults(): array {
        try {
            $stmt = $this->conn->prepare("SELECT results.*, s.first AS student_first, s.last AS student_last, a.first AS advisor_first, 
            a.last AS advisor_last, courses.course_title FROM results JOIN users s ON results.student_id = s.id JOIN users a ON
            results.advisor_id = a.id JOIN courses ON results.courses_id = courses.id ORDER BY results.uploaded_at DESC ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("getAllResults error: " .$e->getMessage());
            return [];
        }

    }

    //Release Results
    public function releaseResults(bool $status): bool {
        $value = $status ? 1 : 0;
        try {
            $stmt = $this->conn->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_key = 'results_released' ");
            $stmt->execute([$value]);
            return true;
        } catch (PDOException $e) {
            error_log('releaseResults error: ' .$e->getMessage());
            return false;
        }
    }


}

?>
