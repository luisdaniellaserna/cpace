import sys
import json
import base64
import random  # Import random for selecting random study resources

# Import or include the detect_weaknesses function from decisionweak.py
from decisionweak import detect_weaknesses

# Define a mapping of study resources per subject
study_resources = {
    'Financial Accounting and Reporting': [
        "Read Foundations of Financial Accounting: Develop a strong understanding of fundamental financial accounting concepts, focusing on essential topics such as the accounting equation, financial statements, and basic accounting principles. Engage in activities like reading and taking notes on assets, liabilities, equity, revenues, and expenses, paying close attention to the definitions and relationships between these elements to reinforce comprehension. Reference: https://annas-archive.org/slow_download/09cacd3cece4f2d3ce944d4d597ef3fa/0/0 ",
        "Video Lecture on Basics: Watch an introductory lecture to reinforce foundational concepts. Reflect on examples used to understand real-world applications. Video: https://www.youtube.com/watch?v=QOO8dCB9ZB0",
        "Concept Mapping Exercise: Build a visual understanding of relationships between accounting concepts by creating a concept map linking key terms.",
        "Real-World Case Study Analysis: Apply concepts to a real-world scenario. Analyze a case study to identify and classify assets, liabilities, and other elements.",
        "End-of-Chapter Summary: Summarize key points from each chapter to reinforce understanding. Identify areas needing further review."
    ],
    'Advanced Financial Accounting and Reporting': [
        "Read Advanced Financial Accounting Materials: Focus on complex topics like consolidation, foreign currency transactions, and financial instruments. Reference advanced textbooks or online materials. https://annas-archive.org/slow_download/42843151d89bf8c11f65dc3a95635a58/0/0", 
        "Video Lecture on Key Topics: Watch a lecture covering advanced topics such as mergers, consolidations, and foreign subsidiaries. https://www.youtube.com/watch?v=jQNyO2HTMLI", 
        "Practice Test: Apply knowledge on complex areas like foreign currency adjustments and intercompany eliminations through practice questions.",
        "Case Study Analysis: Work on a case that involves consolidation or derivative usage, focusing on adjustments and financial statement impacts.",
        "Standards Comparison: Review IFRS vs. GAAP on advanced topics, especially leases, revenue recognition, and fair value.",
        "End-of-Chapter Summary: Summarize key points and identify areas for further study."
    ],
    'Management Services': [
        "Read Core Textbook: Management Services by Bernard W. Taylor and Mary K. Lind This book covers essential topics like productivity improvement, process optimization, decision-making tools, and organizational efficiency. It’s ideal for developing a broad understanding of management consulting and services.", 
        "Video Lecture: Introduction to Management Consulting This video provides insights into management services, focusing on topics like process improvement, strategy, and operations management. Watch here: https://www.youtube.com/watch?v=iq4o1VtHYdA ", 
        "Practice Case Study Analysis: Study a real-world case related to management services, focusing on identifying efficiency gaps, proposing improvements, and evaluating implementation.",
        "Concept Mapping Exercise: Create a visual map linking core management service functions, such as productivity tools, workflow optimization, and resource management.",
        "Self-Assessment: Summarize key concepts and identify any topics for further review."
        ],
    'Auditing': [
        "Read Principles of Auditing: Understand the purpose and types of audits, including internal, external, and forensic audits. Reference: https://annas-archive.org/slow_download/2c4fe5fb218057ff4a63076d56a5ffe1/0/0",
        "Video Lecture on Auditing Principles: Reinforce knowledge on key principles like materiality and risk assessment. Video: https://www.youtube.com/watch?v=I7QAOuwm6Qg",
        "Auditing Standards Review: Research and compare key auditing standards (ISA or GAAS) and their implications.",
        "Practical Audit Case Study: Analyze a real-world scenario, identifying risks and controls, and tailoring the audit approach.",
        "Mock Audit Report: Draft an audit report based on hypothetical findings and seek peer feedback."
    ],
    'Taxation': [
        "Read Taxation Notes: Understand fundamental taxation concepts, including types of taxes and their purpose. Focus on income tax, sales tax, property tax, etc. Reference: https://annas-archive.org/slow_download/48c22eb5227fce840f452a02356f4b2e/0/0 ",
        "Video Lecture on Tax Laws: Learn about current tax laws and recent changes. Reflect on how these laws impact individuals and businesses. Video: https://www.youtube.com/watch?v=Cox8rLXYAGQ",
        "Tax Practice Problems: Work through various taxation scenarios including tax calculations, deductions, and credits.",
        "Taxation Case Study Analysis: Apply taxation principles in real-world examples. Reflect on tax obligations and strategies.",
        "End-of-Chapter Summary: Summarize key taxation points. Identify areas for further review."
    ],
    'Regulatory Framework for Business Transaction': [
        "Read Introductory Material on Regulatory Frameworks: Understand the basic concepts and significance in business. Reference: https://annas-archive.org/slow_download/2f75cda577c3bdd7e738d80b0747c3b2/0/0",
        "Research Key Regulatory Agencies: Study roles of major agencies like the SEC and FTC relevant to your jurisdiction.",
        "Chapter Review on Legal Framework Overview: Review key legal principles affecting business transactions, such as contract and corporate law.",
        "Case Law Analysis: Select landmark cases impacting business regulations. Prepare a brief presentation discussing their implications.",
        "Study Compliance Programs: Explore the importance of compliance and ethics, researching effective compliance program elements.",
        "Video on Regulatory Frameworks: Visualize regulatory frameworks and their importance in business transactions."
    ]
}

# Function to generate a study plan based on weaknesses
def generate_study_plan(weak_subjects):
    study_plan = {}
    for i, subject in enumerate(study_resources.keys()):
        if weak_subjects[i] == 1:  # If the subject is marked as weak
            # Select 3 random study resources for each subject
            study_plan[subject] = random.sample(study_resources[subject], 3)
    return study_plan

# Silence any unexpected outputs
sys.stdout = open(sys.stdout.fileno(), mode='w', encoding='utf8', buffering=1)

try:
    # Get JSON-encoded string from decision.php (in base64 format)
    encoded_input = sys.argv[1]
    decoded_data = base64.b64decode(encoded_input).decode('utf-8')
    student_data = json.loads(decoded_data)

    # Detect weaknesses in student data using decisionweak.py's function
    weak_subjects = detect_weaknesses(student_data)

    # Generate a study plan for weak subjects
    study_plan = generate_study_plan(weak_subjects)

    # Combine weaknesses and study plan into a single JSON object
    output = {
        "weaknesses": weak_subjects,
        "study_plan": study_plan
    }

    # Print only the combined JSON object as the output
    print(json.dumps(output))

except Exception as e:
    # Print any errors as JSON to ensure structured output
    error_output = {"error": str(e)}
    print(json.dumps(error_output))
