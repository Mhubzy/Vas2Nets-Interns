<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../Logs/error.log');

require_once __DIR__. "/../classes/Database.php";

class Advisor {

    private PDO $conn; 

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    //_______MY STUDENTS_________

    //Get all students assigned to the advisor
    public function getMyStudents(int $advisor_id): array {
        try {
            $stmt = $this->conn->prepare("SELECT users.id, users.first, users.last, users.email, users.profile_image,   
            advisor_students.assigned_at FROM advisor_students JOIN users ON advisor_students.student_id = users.id WHERE 
            advisor_students.advisor_id = ? ORDER BY advisor_students.assigned_at DESC ");
            $stmt->execute([$advisor_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("getMyStudents error: " . $e->getMessage());
            return [];
        }
    }

    //Search My Students
    public function searchMyStudents(int $advisor_id, string $keyword): array {
        try {
            $like = '%' .$keyword. '%';
            $stmt = $this->conn->prepare("SELECT users.id, users.first, users.last, users.email, users.profile_image, 
            advisor_students.assigned_at FROM advisor_students JOIN users ON advisor_students.student_id = users.id WHERE
            advisor_students.advisor_id = ? A+ND (users.first LIKE ? OR users.last LIKE ? OR users.email LIKE ?) ORDER BY users.first ASC");
            $stmt->execute([$advisor_id, $like, $like, $like]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("searchMyStudents error: " . $e->getMessage());
            return [];
        }
    }

    //Get one student only if assigned to the advisor
    public function getMyStudentById(int $advisor_id, int $student_id): array|false {
        try {
            $stmt = $this->conn->prepare("SELECT users.id, users.first, users.last, users.email, users.profile_image, users.created_at FROM
            advisor_students JOIN users ON advisor_students.student_id = users.id WHERE advisor_students.advisor_id = ? AND
            advisor_students.student_id = ? ");
            $stmt->execute([$advisor_id, $student_id]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            error_log("getMyStudentById error: " .$e->getMessage());
            return false;
        }
    }

    //_______COURSES__________
    //Get all courses a student is enrolled in, only if the student is assigned to the advisor
    public function getStudentCourses(int $advisor_id, int $student_id): array {
        try {
            //Verify the student is assigned to the advisor
            $check = $this->conn->prepare("SELECT id FROM advisor_students WHERE advisor_id = ? AND student_id = ?");
            $check->execute([$advisor_id, $student_id]);
            if(!$check->fetch()) {
                return [];
            }
            $stmt = $this->conn->prepare("SELECT courses.id, courses.course_title, courses.course_code, courses.description, 
            student_courses.enrolled_at FROM student_courses JOIN courses ON student_courses.course_id = courses.id WHERE
            student_courses.student_id = ? ORDER BY student_courses.enrolled_at DESC");
            $stmt->execute([$student_id]);
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("getStudentCourses error: " .$e->getMessage());
            return [];
        }
    }
    
    //Get all courses assigned to the advisor
    public function getMyCourses(int $advisor_id): array {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM courses WHERE advisor_id = ? ORDER BY created_at DESC");
            $stmt->execute([$advisor_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log ("getMyCourses error: " .$e->getMessage());
            return[];
        }
    }

    //Get student enrolled in courses
    public function getStudentInCourse(int $advisor_id, int $course_id): array {
        try {
            //Verify course belongs to advisor
            $verify = $this->conn->prepare("SELECT id FROM courses WHERE id = ? AND advisor_id = ? ");
            $verify->execute([$course_id, $advisor_id]);

            if(!$verify->fetch()) {
                return [];
            }

            //Get Students
            $stmt = $this->conn->prepare("SELECT users.id, users.first, users.last, users.email FROM student_courses JOIN users ON 
            student_courses.student_id = users.id WHERE student_courses.course_id = ? ");
            $stmt->execute([$course_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("getStudentInCourse error: " .$e->getMessage());
            return[];
        }
    }

    //Get course details only if the course belongs to one of the advisor's students
    public function getCoursesByAdvisor(int $advisor_id): array {
        try {
            $stmt =$this->conn->prepare("SELECT * FROM courses WHERE advisor_id = ? ORDER BY created_at DESC");
            $stmt->execute([$advisor_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("getCoursesByAdvisor error: " .$e->getMessage());
            return [];
        }
    }

    //____________ASSIGNMENTS__________
    public function getStudentAssignments(int $advisor_id, int $student_id): array {
        try {
            //Verify the student is assigned to the advisor
            $check = $this->conn->prepare("SELECT id FROM advisor_students WHERE advisor_id = ? AND student_id = ?");
            $check->execute([$advisor_id, $student_id]);
            if(!$check->fetch()) {
                return [];
            }

            $stmt = $this->conn->prepare("SELECT assignments.id, assignments.filename, assignments.submitted_at, courses.course_title, 
            courses.course_code, results.score, results.comments, results.uploaded_at AS graded_at FROM assignments JOIN courses ON
            assignments.course_id = courses.id LEFT JOIN results ON results.student_id = assignments.student_id AND 
            results.course_id = assignments.course_id WHERE assignments.student_id = ? ORDER BY assignments.submitted_at DESC");
            $stmt->execute([$student_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("getStudentAssignments error: " .$e->getMessage());
            return [];
        }
    }

    //Get all assignments submitted for a course
    public function getCourseAssignments(int $advisor_id, int $course_id): array {
        try {
            //Verify course belongs to the advisor
            $verify = $this->conn->prepare("SELECT id FROM courses WHERE id = ? AND advisor_id = ?");
            $verify->execute([$course_id, $advisor_id]);

            if(!$verify->fetch()) {
                return [];
            }
            
            //Get Assignments
            $stmt = $this->conn->prepare("SELECT assignments.id, assignments.filename, assignments.submitted_at, users.first, users.last,
            users.email, results.score, results.comments, results.uploaded_at AS graded_at FROM assignments JOIN users ON 
            assignments.student_id = users.id LEFT JOIN results ON results.student_id = assignments.student_id AND 
            results.course_id = assignments.course_id WHERE assignments.course_id = ? ORDER BY assignments.submitted_at DESC ");
            $stmt->execute([$course_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("getCourseAssignments error: " .$e->getMessage());
            return[];
        }
    }

    //Get All assignments from all of the advisor's students
    public function getAllMyAssignments(): array {
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

    //Grade and Feedback on assignments
    public function gradeAssignment(int $advisor_id, int $assignment_id, string $score, string $comments): array {
        if(empty(trim($score))) {
            return ['success' => false, 'error' => 'Score is required'];
        }

        try {
            //Verify the assignment belongs to one of the advisor's students
            $check = $this->conn->prepare("SELECT assignments.student_id, assignments.course_id FROM assignments JOIN advisor_students ON 
            advisor_students.student_id = assignments.student_id  WHERE assignments.id = ? AND advisor_students.advisor_id = ?");
            $check->execute([$assignment_id, $advisor_id]);
            $row = $check->fetch();
            if(!$row) {
                return ['success' => false, 'error' => 'Assignment not found or access denied'];
            }

            $student_id = $row['student_id'];
            $course_id  = $row['course_id'];

            //Check if result already exists for this student + course
            $existing = $this->conn->prepare("SELECT id FROM results WHERE student_id = ? AND course_id = ? ");
            $existing->execute([$student_id, $course_id]);

            if($existing->fetch()) {
                //Update existing result
                $stmt = $this->conn->prepare("UPDATE results SET score = ?, comments = ?, uploaded_at = CURRENT_TIMESTAMP 
                WHERE student_id = ? AND course_id = ? ");
                $stmt->execute([trim($score), trim($comments), $student_id, $course_id]);
            } else {
                //Insert new result
                $stmt = $this->conn->prepare("INSERT INTO results (student_id, advisor_id, course_id, score, grade_letter, comments) 
                VALUES (?, ?, ?, ?, '', ?) ");
                $stmt->execute([$student_id, $advisor_id, $course_id, trim($score), trim($comments)]);
            }
            return ['success' => true];
        }catch(PDOException $e) {
            error_log("gradeAssignment error: " .$e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    //_______DASHBOARD__________
    //Get total number of students assigned to the advisor
    public function getTotalMyStudents(int $advisor_id): int {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM advisor_students WHERE advisor_id = ? ");
            $stmt->execute([$advisor_id]);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("getTotalMyStudents error: " .$e->getMessage());
            return 0;
        }
    }

    //Get total number of assignments students submitted
    public function getTotalMyAssignments(int $advisor_id): int {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM assignments JOIN advisor_students ON assignments.student_id =
            advisor_students.student_id WHERE advisor_students.advisor_id = ?");
            $stmt->execute([$advisor_id]);
            return (int)$stmt->fetchColumn();
        } catch(PDOException $e) {
            error_log("getTotalMyAssignments error: " .$e->getMessage());
            return 0;
        }
    }

    //Get total number of courses students are enrolled in
    public function getTotalMyCourses(int $advisor_id): int {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM courses WHERE advisor_id = ?");
            $stmt->execute([$advisor_id]);
            return (int) $stmt->fetchColumn();
        } catch(PDOException $e) {
            error_log("getTotalMyCourses error: " .$e->getMessage());
            return 0;
        }
    }

    //Get total total grade of students
    public function getTotalGraded(int $advisor_id): int {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM results WHERE advisor_id = ?");
            $stmt->execute([$advisor_id]);
            return (int) $stmt->fetchColumn();
        } catch(PDOException $e) {
            error_log("getTotalGraded error: " .$e->getMessage());
            return 0;
        }
    }

    //Get recent student for the dashboard
    public function getRecentMyStudents(int $advisor_id, int $limit = 5): array {
        try {
            $stmt = $this->conn->prepare("SELECT users.id, users.first, users.last, users.email, advisor_students.assigned_at FROM
            advisor_students JOIN users on advisor_students.student_id = users.id WHERE advisor_students.advisor_id = ? ORDER BY
            advisor_students.assigned_at DESC LIMIT ?");
            $stmt->execute([$advisor_id, $limit]);
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("getRecentStudent error: " .$e->getMessage());
            return [];
        }
    }

    //Get recent assignments for the dashboard
    public function getRecentMyAssignments(int $advisor_id, int $limit = 5): array {
        try {
            $stmt = $this->conn->prepare("SELECT assignments.id, assignments.course_title, assignments.course_code, assignments.submitted_At,
            users.first, users.last, results.score FROM assignments JOIN users ON assignments.student_id = users.id JOIN advisor_students
            ON users.id = advisor_students.student_id LEFT JOIN results ON assignments.id = results.assignment_id WHERE
            advisor_students.advisor_id = ? ORDER BY assignments.submitted_at DESC LIMIT ? ");
            $stmt->execute([$advisor_id, $limit]);
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("getRecentMyAssignments error: " .$e->getMessage());
            return [];
        }
    }

    //_____________RESULTS MANAGEMENT________________

    //Upload results for a student, only if the student is assigned to the advisor
    public function uploadResult(int $advisor_id, int $student_id, int $course_id, float $score, string $grade_letter, string $comments):array {
        try {
            //Check if student is enrolled in the course and assigned to the advisor
            $check = $this->conn->prepare("SELECT student_courses.id FROM student_courses JOIN courses ON student_courses.course_id = courses.id
            WHERE student_courses.student_id = ? AND student_courses.course_id = ? ");
            $check->execute([$student_id, $course_id]);

            if(!$check->fetch()) {
                return ['success' => false, 'error' => 'Student is not enrolled in the selected course.'];
            }

            //Check if results already exist

            $existing = $this->conn->prepare("SELECT id FROM results WHERE student_id = ? AND course_id = ?");
            $existing->execute([$student_id, $course_id]);
            
            //Update existing results
            if($existing->fetch()) {
                $update = $this->conn->prepare("UPDATE results SET score = ?, grade_letter = ?, comments = ?, graded_at = CURRENT_TIMESTAMP 
                WHERE student_id = ? AND course_id = ?");
                $update->execute([$score, $grade_letter, $comments, $student_id, $course_id]);
            } else {

                //Insert new results

                $insert = $this->conn->prepare("INSERT INTO results (student_id, course_id, advisor_id, score, grade_letter, comments) 
                VALUES (?, ?, ?, ?, ?, ?)");
                $insert->execute([$student_id, $course_id, $advisor_id, $score, $grade_letter, $comments]);
            }
            return ['success' => true];

        } catch(PDOException $e) {
            error_log("uploadResult error: " .$e->getMessage());
            return ['success' => false, 'error' => 'Could not upload results.'];
        }
    }

}

?>
