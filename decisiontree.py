import sys
import json
import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.tree import DecisionTreeClassifier
from sklearn.metrics import accuracy_score, classification_report

# Get JSON data from the PHP script (student scores)
input_data = sys.argv[1]

# Decode the JSON input
student_data = json.loads(input_data)

# Convert input data into DataFrame for prediction
df = pd.DataFrame([student_data])

# Define the threshold for weakness (score below 6 is considered weak)
weakness_threshold = 6

# Sample dataset (students' mock test scores out of 10)
data = {
    'Financial Accounting and Reporting': [6, 8, 5, 9, 4, 8],
    'Advanced Financial Accounting and Reporting': [8, 8, 6, 9, 5, 8],
    'Management Services': [6, 8, 6, 10, 6, 8],
    'Auditing': [7, 9, 7, 10, 7, 9],
    'Taxation': [6, 8, 6, 8, 5, 8],
    'Regulatory Framework for Business Transaction': [6, 8, 6, 9, 6, 8],
}

# Generate the target 'Weakness' based on the threshold (1 = Weak, 0 = Not weak)
weakness = [1 if score < weakness_threshold else 0 for score in data['Financial Accounting and Reporting']]

# Add the generated 'Weakness' column to the dataset
data['Weakness'] = weakness

# Create DataFrame from the dataset
df_data = pd.DataFrame(data)

# Features (mock test scores)
X = df_data.drop(columns=['Weakness'])

# Target (whether the student is weak)
y = df_data['Weakness']

# Split the dataset into training and testing sets (80% training, 20% testing)
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

# Initialize the Decision Tree Classifier
clf = DecisionTreeClassifier(random_state=42)

# Train the model
clf.fit(X_train, y_train)

# Evaluate the model
y_pred = clf.predict(X_test)

# Calculate and print the accuracy of the model
accuracy = accuracy_score(y_test, y_pred)
print(f"Accuracy: {accuracy * 100:.2f}%")

# Display a detailed classification report
print("Classification Report:")
print(classification_report(y_test, y_pred))

# For predictions, apply the manual threshold logic (weakness if score < 6)
def generate_prediction(scores):
    return [1 if score < weakness_threshold else 0 for score in scores]

# Predict weaknesses for the new student data (passed from PHP)
predictions = generate_prediction(df.iloc[0].values)

# Return the predictions as JSON
print(json.dumps(predictions))
