<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Includes/Student_auth.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Student.php';

use Dompdf\Dompdf;

$user   = new User();
$student = new Student();

$student_id = $_SESSION['user_id'];

//Prevent viewing before release
if (!$student->canViewResults()) {
    die("Results have not been released yet.");
}

//Get student info
$student = $user->findById($student_id);

//Get results
$results = $student_id->getStudentResults($student_id);

//Create HTML
$html = '
<h1 style="text-align:center;">SCHOOL PORTAL RESULT</h1>

<hr>

<h3>Student Information</h3>

<p>
<strong>Name:</strong> ' . htmlspecialchars($student['first'] . ' ' . $student['last']) . '<br>

<strong>Email:</strong> ' . htmlspecialchars($student['email']) . '

<strong>Matric Number:</strong> ' . 'STU-' . (!empty($student['id']) ? str_pad($student['id'], 5, '0', STR_PAD_LEFT) : '00000') . '
</p>

<br>

<table width="100%" border="1" cellspacing="0" cellpadding="8">

<tr>
    <th>Course</th>
    <th>Score</th>
    <th>Grade</th>
    <th>Comment</th>
</tr>
';

foreach ($results as $r) {

    $html .= '
    <tr>

        <td>' . htmlspecialchars($r['course_title']) . '</td>

        <td>' . htmlspecialchars($r['score']) . '</td>

        <td>' . htmlspecialchars($r['grade_letter']) . '</td>

        <td>' . htmlspecialchars($r['comment']) . '</td>

    </tr>
    ';
}

$html .= '</table>

<br><br>

<p>
Generated on: ' . date('F j, Y g:i A') . '
</p>
';

//Generate PDF
$dompdf = new Dompdf();

$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

//Download PDF
$dompdf->stream(
    "Result_" . $student['first'] . ".pdf",
    ["Attachment" => true]
);