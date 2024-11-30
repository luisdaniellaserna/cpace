<?php
// Enable error reporting for debugging

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to the MySQL database
$conn = new mysqli('localhost', 'root', '', 'cpa');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if student_Id is set from the POST request
if (isset($_POST['student_Id'])) {
    $student_Id = intval($_POST['student_Id']); // Sanitize the input to ensure it's an integer

    // Query to get the latest 5 quiz attempts for the student, sorted by date_taken
    $sql = "SELECT * FROM score_history WHERE student_Id = $student_Id ORDER BY exam_date DESC LIMIT 5";
    $result = $conn->query($sql);

    // If data is found
    if ($result && $result->num_rows > 0) {
        $student_data = [];

        // Collect the 5 latest quiz scores for each subject
        while ($row = $result->fetch_assoc()) {
            $student_data[] = array(
                'Financial Accounting and Reporting' => $row['Financial_Score'],
                'Advanced Financial Accounting and Reporting' => $row['Adv_Score'],
                'Management Services' => $row['Mng_score'],
                'Auditing' => $row['Auditing_Score'],
                'Taxation' => $row['Taxation_Score'],
                'Regulatory Framework for Business Transaction' => $row['Framework_score']
            );
        }

        // Calculate the average score for each subject over the 5 latest quizzes
        $averaged_scores = [];
        foreach ($student_data[0] as $subject => $score) {
            $total = 0;
            foreach ($student_data as $quiz) {
                $total += $quiz[$subject];
            }
            $averaged_scores[$subject] = $total / count($student_data);
        }

        // Convert averaged scores array to JSON and encode with base64
        $input_json = json_encode($averaged_scores);
        $encoded_json = base64_encode($input_json);

        // Full path to Python executable on Windows
        $python_path = 'C:\\Users\\Msi\\AppData\\Local\\Microsoft\\WindowsApps\\python3.exe';  // Replace with your Python path

        // Path to hybrid.py script
        $hybrid_script = 'C:\\xampp\\htdocs\\CPA\\hybrid.py';  // Replace with the full path to hybrid.py

        // Call hybrid.py to generate the study plan
        $command = escapeshellcmd($python_path . ' ' . $hybrid_script . ' ' . escapeshellarg($encoded_json)) . ' 2>&1';
        $hybrid_output = shell_exec($command);
        $hybrid_output = trim($hybrid_output);

        // Decode the combined JSON output from hybrid.py
        $decoded_output = json_decode($hybrid_output, true);

        // Check for JSON errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "JSON Decode Error: " . json_last_error_msg() . "<br>";
            echo "Output received from hybrid.py: " . htmlspecialchars($hybrid_output);
        } else {
            // Extract weaknesses and study plan from the decoded output
            $weaknesses = $decoded_output['weaknesses'];
            $study_plan = $decoded_output['study_plan'];

            // Display the weaknesses
            echo "<h1>Student ID: $student_Id</h1>";
            echo "<h3>Weakness Predictions:</h3>";
            $subjects = array(
                'Financial Accounting and Reporting',
                'Advanced Financial Accounting and Reporting',
                'Management Services',
                'Auditing',
                'Taxation',
                'Regulatory Framework for Business Transaction'
            );

            foreach ($subjects as $index => $subject) {
                echo "<p><strong>{$subject}:</strong> " . ($weaknesses[$index] == 1 ? "Weak" : "Good") . "</p>";
            }

            // Display the study plan with bullets
            echo "<h3>Generated Study Plan:</h3>";
            foreach ($study_plan as $subject => $resources) {
                echo "<h4>Study Plan for: $subject</h4><ul>";
                foreach ($resources as $resource) {
                    echo "<li>$resource</li>";
                }
                echo "</ul>";
            }
        }
    } else {
        echo "No student data found.";
    }
} else {
    echo "Student ID is not set.";
}

// Close the connection
$conn->close();
?>
