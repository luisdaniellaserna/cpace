<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Add Question</title>
  <link href='Style/QAdd.css' rel='stylesheet' type='text/css'>
</head>
<body>
<div class="taskbar">
    <div class="logo"></div>
<h1 class="white-text">Auditing</h1>
    <a href="q4.php" class="back-button">&#8592; Back</a>
</div>
<form class="section" action="q4php.php" method="post" enctype="multipart/form-data">
  <h1>Add Questions</h1>

  <!-- Scenario Section -->
  <div class="scenarioInput">
    <h3>Scenario (Optional):</h3>
    <textarea name="scenario" placeholder="Provide a scenario or background for the questions" rows="4" maxlength="1000" oninput="updateCharacterCount('scenario', 1000)"></textarea>
    <span id="scenarioCount">0/1000</span>
  </div>

  <!-- Scenario Image Section -->
  <div class="scenarioImageInput">
    <h3>Attach a Scenario Image (optional):</h3>
    <input type="file" name="scenarioImage" accept="image/*" onchange="previewScenarioImage(event)" />
    <div class="imagePreview" id="scenarioImagePreview" style="margin-top: 10px; position: relative;">
        <p>No image selected.</p>
    </div>
  </div>

  <!-- Dynamic Questions Container -->
  <div id="questionsContainer">
    <!-- Initial Question Section (it will be copied when a new question is added) -->
    <div class="questionSection" id="questionSection1">
      <h3>Question 1</h3>
      
      <!-- Question Text Input -->
      <div class="textQuestionInput">
        <label for="questionText1">Type Your Question:</label>
        <textarea id="questionText1" name="questionText[]" placeholder="Input your question here." required maxlength="500" oninput="updateCharacterCount('questionText1', 500)"></textarea>
        <span id="questionText1Count">0/500</span>
      </div>

      <!-- Image Question Input -->
      <div class="imageQuestionInput">
        <h3>Attach an Image for Question (optional):</h3>
        <input type="file" name="questionImage[]" accept="image/*" onchange="previewImage(event, 1)" />
        <div class="imagePreview" id="imagePreview1" style="margin-top: 10px; position: relative;">
            <p>No image selected.</p>
        </div>
      </div>

      <!-- Choices and Answer Selection -->
      <div class="choicesInput">
        <h3>Choices:</h3>
        <input name="opt1[]" type="text" placeholder="A." required />
        <input name="opt2[]" type="text" placeholder="B." required />
        <input name="opt3[]" type="text" placeholder="C." required />
        <input name="opt4[]" type="text" placeholder="D." required />
      </div>
      
      <div class="answerInput">
        <h3>Correct Answer:</h3>
        <select name="answer[]" required>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
        </select>
      </div>

      <!-- Topic Selection -->
      <div class="topicInput">
        <h3>Topic:</h3>
        <select name="topic[]" required>
            <option value="Fundamentals">Fundamentals of auditing and assurance services</option>
            <option value="Risk-based">Risk-based Financial Statement Audit</option>
            <option value="Understanding">Understanding the Entity and Internal Control</option>
            <option value="Audit Evidence">Audit Evidence and Documentation</option>
            <option value="Audit Completion">Audit Completion and Reporting</option>
            <option value="CIS">Auditing in CIS Environment</option>
            <option value="Attestation">Attestation Services</option>
            <option value="Governance">Governance, Ethics, and Quality Management</option>
            <option value="Risk Response">Risk Response and Reporting</option>
        </select>
      </div>
    </div>
  </div>

  <!-- Increment/Decrement Controls -->
  <div>
    <button type="button" onclick="addQuestion()">Add More Question</button>
    <button type="button" onclick="removeQuestion()">Remove Question</button>
  </div>

  <br>
  <input class="btn" type="submit" value="Add Question/s" />
</form>

<script>
// JavaScript to preview scenario image
function previewScenarioImage(event) {
    const imagePreview = document.getElementById('scenarioImagePreview');
    imagePreview.innerHTML = ''; // Clear previous content

    const file = event.target.files[0];

    if (file) {
        const imgWrapper = document.createElement('div');
        imgWrapper.style.position = 'relative';
        imgWrapper.style.display = 'inline-block';

        const imgElement = document.createElement('img');
        imgElement.src = URL.createObjectURL(file);
        imgElement.style.maxWidth = '200px';
        imgElement.style.marginTop = '10px';
        imgElement.style.border = '1px solid #ccc';
        imgElement.style.borderRadius = '5px';

        const removeButton = document.createElement('span');
        removeButton.textContent = '×';
        removeButton.className = 'removeButton';

        removeButton.addEventListener('click', () => {
            imagePreview.innerHTML = '<p>No image selected.</p>';
            document.querySelector('input[name="scenarioImage"]').value = ''; // Clear the input
        });

        imgWrapper.appendChild(imgElement);
        imgWrapper.appendChild(removeButton);
        imagePreview.appendChild(imgWrapper);
    } else {
        imagePreview.innerHTML = '<p>No image selected.</p>';
    }
}

// JavaScript to preview images for questions
function previewImage(event, questionId) {
    const imagePreview = document.getElementById(`imagePreview${questionId}`);
    imagePreview.innerHTML = ''; // Clear previous content

    const file = event.target.files[0];

    if (file) {
        const imgWrapper = document.createElement('div');
        imgWrapper.style.position = 'relative';
        imgWrapper.style.display = 'inline-block';

        const imgElement = document.createElement('img');
        imgElement.src = URL.createObjectURL(file);
        imgElement.style.maxWidth = '200px';
        imgElement.style.marginTop = '10px';
        imgElement.style.border = '1px solid #ccc';
        imgElement.style.borderRadius = '5px';

        const removeButton = document.createElement('span');
        removeButton.textContent = '×';
        removeButton.className = 'removeButton';

        removeButton.addEventListener('click', () => {
            imagePreview.innerHTML = '<p>No image selected.</p>';
            document.querySelector(`#questionSection${questionId} input[name="questionImage[]"]`).value = '';
        });

        imgWrapper.appendChild(imgElement);
        imgWrapper.appendChild(removeButton);
        imagePreview.appendChild(imgWrapper);
    } else {
        imagePreview.innerHTML = '<p>No image selected.</p>';
    }
}

// JavaScript to add a new question
let questionCount = 1;
function addQuestion() {
    questionCount++;
    const questionsContainer = document.getElementById('questionsContainer');

    // Create new question section
    const newQuestionSection = document.createElement('div');
    newQuestionSection.classList.add('questionSection');
    newQuestionSection.id = `questionSection${questionCount}`;
    
    newQuestionSection.innerHTML = `
        <h3>Question ${questionCount}</h3>
        
        <div class="textQuestionInput">
          <label for="questionText1">Type Your Question:</label>
          <textarea id="questionText1" name="questionText[]" placeholder="Input your question here." required maxlength="500" oninput="updateCharacterCount('questionText1', 500)"></textarea>
          <span id="questionText1Count">0/500</span>
        </div>

        <div class="imageQuestionInput">
            <h3>Attach an Image for Question (optional):</h3>
            <input type="file" name="questionImage[]" accept="image/*" onchange="previewImage(event, ${questionCount})" />
            <div class="imagePreview" id="imagePreview${questionCount}" style="margin-top: 10px; position: relative;">
                <p>No image selected.</p>
            </div>
        </div>

        <div class="choicesInput">
            <h3>Choices:</h3>
            <input name="opt1[]" type="text" placeholder="A." required />
            <input name="opt2[]" type="text" placeholder="B." required />
            <input name="opt3[]" type="text" placeholder="C." required />
            <input name="opt4[]" type="text" placeholder="D." required />
        </div>
        
        <div class="answerInput">
            <h3>Correct Answer:</h3>
            <select name="answer[]" required>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
            </select>
        </div>

        <!-- Topic Selection -->
        <div class="topicInput">
            <h3>Topic:</h3>
            <select name="topic[]" required>
                <option value="Fundamentals">Fundamentals of auditing and assurance services</option>
                <option value="Risk-based">Risk-based Financial Statement Audit</option>
                <option value="Understanding">Understanding the Entity and Internal Control</option>
                <option value="Audit Evidence">Audit Evidence and Documentation</option>
                <option value="Audit Completion">Audit Completion and Reporting</option>
                <option value="CIS">Auditing in CIS Environment</option>
                <option value="Attestation">Attestation Services</option>
                <option value="Governance">Governance, Ethics, and Quality Management</option>
                <option value="Risk Response">Risk Response and Reporting</option>
            </select>
        </div>
    `;

    // Append the new question section
    questionsContainer.appendChild(newQuestionSection);
}

// Remove last question function
function removeQuestion() {
    const questionSections = document.querySelectorAll('.questionSection');
    if (questionSections.length > 1) {
        questionSections[questionSections.length - 1].remove();
    }
}

// JavaScript function to update the character count
function updateCharacterCount(textareaId, maxLength) {
  // Get the textarea and the counter span element by the ID
  const textarea = document.querySelector(`[name="${textareaId}"], #${textareaId}`);
  const countSpan = document.getElementById(`${textareaId}Count`);
  
  // Get the current length of the input text
  const currentLength = textarea.value.length;
  
  // Update the counter text with current length and the max length
  countSpan.textContent = `${currentLength}/${maxLength}`;
}

</script>
</body>
</html>
