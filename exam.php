<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Exam</title>
    <link href="Style/exam.css" rel="stylesheet" type="text/css"> <!-- Link to external CSS -->
</head>
<body>

<div class="container">
    <h2>Online Exam</h2>
    <?php
    // Start output buffering
    ob_start();

    // Start session to access session variables
    session_start();

    // Ensure the user is logged in by checking if the username session variable is set
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
    } else {
        // If the session variable is not set, redirect the user to the login page
        header("Location: index.php");
        exit();
    }

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'cpa');

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Array to store questions from all tables
    $allQuestions = [];

    // Function to fetch 10 random questions from a specific table with a custom ID column
    function fetchRandomQuestions($conn, $tableName, $idColumn) {
        // Select the specific ID column as 'id' so we can reference it uniformly
        $sql = "SELECT $idColumn AS id, question, opt1, opt2, opt3, opt4, answer FROM $tableName ORDER BY RAND() LIMIT 10";
        $result = $conn->query($sql);

        if (!$result) {
            die("Query failed for table '$tableName': " . $conn->error);
        }

        $questions = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['table'] = $tableName;  // Add table name as a separate field for tracking
                $questions[] = $row;
            }
        }
        return $questions;
    }

    // Fetch 10 random questions from each table with specified ID columns and merge into a single array
    $allQuestions = array_merge(
        fetchRandomQuestions($conn, 'financial', 'fin_id'),
        fetchRandomQuestions($conn, 'aud', 'aud_id'),
        fetchRandomQuestions($conn, 'adv', 'adv_id'),
        fetchRandomQuestions($conn, 'mng', 'mng_id'),
        fetchRandomQuestions($conn, 'tax', 'tax_id'),
        fetchRandomQuestions($conn, 'reg', 'reg_id')
    );

    // Shuffle all questions
    shuffle($allQuestions);

    // Start building the form to display the exam
    echo '<form id="examForm" action="submit.php" method="post">';

    // Initialize question number counter
    $questionNumber = 1;

    // Loop through each question and display it
    foreach ($allQuestions as $question) {
        // Replace the [Image: path] with an actual <img> tag and add a <br> before the image for better spacing
        $questionText = preg_replace(
            '/\[Image:\s*(.+?)\]/i',
            '<br><img src="$1" alt="Question Image"><br>',
            htmlspecialchars($question['question'])
        );

        // Create a unique identifier based on the table name and the question's ID
        $uniqueId = $question['table'] . "_" . $question['id'];
        
        // Display question number and modified question text
        echo "<div class='question-container' id='question-$uniqueId'>";
        echo "<p><strong>Question $questionNumber: </strong><br>" . $questionText . "</p>";

        // Randomize the choices
        $choices = [
            'A' => $question['opt1'],
            'B' => $question['opt2'],
            'C' => $question['opt3'],
            'D' => $question['opt4']
        ];
        $shuffledChoices = [];
        $keys = array_keys($choices);
        shuffle($keys);
        foreach ($keys as $key) {
            $shuffledChoices[$key] = $choices[$key];
        }

        // Display each choice as a radio button with a descriptive and unique name based on the unique ID
        foreach ($shuffledChoices as $key => $choiceText) {
            echo "<label><input type='radio' name='answers[$uniqueId]' value='$key' required> " . htmlspecialchars($choiceText) . "</label><br>";
        }

        echo "</div><br>"; // Close question-container and add some spacing after each question

        // Increment the question number
        $questionNumber++;
    }

    // Add a submit button for the exam
    echo '<button type="submit">Submit Exam</button>';
    echo '</form>';

    // Close the database connection
    $conn->close();
    ?>
</div>

<!-- JavaScript for validating unanswered questions -->
<script>
    document.getElementById('examForm').addEventListener('submit', function(event) {
        // Get all question containers
        const questionContainers = document.querySelectorAll('.question-container');
        let allAnswered = true;

        // Loop through each question container to check if it is answered
        questionContainers.forEach(container => {
            const questionId = container.id.split('-')[1]; // Get question ID
            const radios = document.getElementsByName('answers[' + questionId + ']');
            let answered = false;

            // Check if any of the radio buttons is checked
            for (let i = 0; i < radios.length; i++) {
                if (radios[i].checked) {
                    answered = true;
                    break;
                }
            }

            // If not answered, mark the container with a red outline
            if (!answered) {
                allAnswered = false;
                container.style.border = '2px solid red';
            } else {
                container.style.border = ''; // Remove the border if already answered
            }
        });

        // If not all questions are answered, prevent form submission and alert the user
        if (!allAnswered) {
            event.preventDefault();
            alert('Please answer all questions before submitting.');
        }
    });
</script>
</body>
</html>
