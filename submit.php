<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'cpa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['answers'])) {
    $userAnswers = $_POST['answers'];
    $correctAnswersCount = 0;
    $totalQuestionsCount = count($userAnswers);

    // Initialize counters for each section
    $advCorrect = $audCorrect = $finCorrect = $mngCorrect = $regCorrect = $taxCorrect = 0;
    $advTotal = $audTotal = $finTotal = $mngTotal = $regTotal = $taxTotal = 0;

    // Define the ID column for each table
    $idColumns = [
        'adv' => 'adv_id',
        'aud' => 'aud_id',
        'financial' => 'fin_id',
        'mng' => 'mng_id',
        'reg' => 'reg_id',
        'tax' => 'tax_id'
    ];

    foreach ($userAnswers as $questionId => $userAnswer) {
        // Split questionId to get the table and question ID, e.g., "adv_1" gives "adv" and "1"
        list($table, $id) = explode('_', $questionId);

        // Validate table name to prevent SQL injection
        if (!array_key_exists($table, $idColumns)) {
            die("Invalid table name.");
        }

        // Get the correct ID column for the current table
        $idColumn = $idColumns[$table];

        // Prepare the SQL statement to fetch the correct answer
        $stmt = $conn->prepare("SELECT answer FROM $table WHERE $idColumn = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($correctAnswer);
        $stmt->fetch();

        // Check if the user's answer matches the correct answer
        if ($userAnswer == $correctAnswer) {
            $correctAnswersCount++;
            // Increment the correct counter for the specific table
            switch ($table) {
                case 'adv':
                    $advCorrect++;
                    break;
                case 'aud':
                    $audCorrect++;
                    break;
                case 'financial':
                    $finCorrect++;
                    break;
                case 'mng':
                    $mngCorrect++;
                    break;
                case 'reg':
                    $regCorrect++;
                    break;
                case 'tax':
                    $taxCorrect++;
                    break;
            }
        }

        // Increment the total counter for the specific table
        switch ($table) {
            case 'adv':
                $advTotal++;
                break;
            case 'aud':
                $audTotal++;
                break;
            case 'financial':
                $finTotal++;
                break;
            case 'mng':
                $mngTotal++;
                break;
            case 'reg':
                $regTotal++;
                break;
            case 'tax':
                $taxTotal++;
                break;
        }

        $stmt->close();
    }

    // Use the raw scores directly
    $Adv_Score = $advCorrect;
    $Auditing_Score = $audCorrect;
    $Financial_Score = $finCorrect;
    $Mng_Score = $mngCorrect;
    $Framework_Score = $regCorrect;
    $Taxation_Score = $taxCorrect;

    // Prepare statement to insert the scores into the score_history table with exam_date
    $stmt = $conn->prepare("INSERT INTO score_history (student_id, Adv_Score, Auditing_Score, Financial_Score, Mng_Score, Framework_Score, Taxation_Score, exam_date) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // Assuming you have `student_id` stored in the session or retrieved from the database
    $studentId = $_SESSION['username']; // Modify this as per your implementation

    $stmt->bind_param("iiiiiii", $studentId, $Adv_Score, $Auditing_Score, $Financial_Score, $Mng_Score, $Framework_Score, $Taxation_Score);
    $stmt->execute();
    $stmt->close();

    // Redirect the user to hp.php
    header("Location: hp.php");
    exit();

} else {
    echo "No answers submitted.";
}

$conn->close();
?>
