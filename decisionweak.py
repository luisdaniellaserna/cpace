import sys
import json
import base64

# Read the base64 encoded JSON string from the command line
input_data = sys.argv[1]

# Decode the base64 string to JSON format
decoded_data = base64.b64decode(input_data).decode('utf-8')

# Parse the JSON string into a Python dictionary
student_data = json.loads(decoded_data)

# Define the threshold for weakness (score below 6 is considered weak)
weakness_threshold = 6

# Generate predictions based on the threshold
def detect_weaknesses(student_data):
    predictions = []
    for score in student_data.values():
        score = int(score)  # Convert the score to an integer
        if score < weakness_threshold:
            predictions.append(1)  # Weak
        else:
            predictions.append(0)  # Not Weak
    return predictions

# Get predictions for the student's scores
predictions = detect_weaknesses(student_data)

# Output the predictions as JSON
print(json.dumps(predictions))
