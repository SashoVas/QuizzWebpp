<?php
require __DIR__ . '/../database/db.php';
require __DIR__ . '/../helpers/auth_helpers.php';

function exportTestToMoodleXML($test_id) {
    //get test and questions from db
    $test = fetchTest($test_id);
    $questions = fetchQuestions($test_id);

    //generate xml document
    $xml = createBaseXML();
    $quiz = createQuizElement($xml);
    addCategory($xml, $quiz, $test['name']);
    
    //add questions
    foreach ($questions as $question) {
        $questionNode = createQuestionNode($xml, $question);
        $quiz->appendChild($questionNode);
    }

    //download xml file result
    outputXML($xml, $test['name']);
}

function fetchTest($test_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM tests WHERE id = ?");
    $stmt->execute([$test_id]);
    $test = $stmt->fetch();
    if (!$test) {
        header("Location: ../pages/main.php?message=error&error=bad_request");
        exit;
    }
    return $test;
}

function fetchQuestions($test_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM questions WHERE test_id = ? ORDER BY id");
    $stmt->execute([$test_id]);
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$questions) {
        header("Location: ../pages/main.php?message=error&error=bad_request");
        exit;
    }
    return $questions;
}

function createBaseXML() {
    $xml = new DOMDocument('1.0', 'UTF-8');
    $xml->formatOutput = true;
    return $xml;
}

function createQuizElement($xml) {
    $quiz = $xml->createElement('quiz');
    $xml->appendChild($quiz);
    return $quiz;
}

function addCategory($xml, $quiz, $test_name) {
    $categoryQuestion = $xml->createElement('question');
    $categoryQuestion->setAttribute('type', 'category');
    
    $category = $xml->createElement('category');
    $categoryText = $xml->createElement('text', '$course$/' . htmlspecialchars($test_name));
    $category->appendChild($categoryText);
    
    $categoryQuestion->appendChild($category);
    $quiz->appendChild($categoryQuestion);
}

function createQuestionNode($xml, $question) {
    $questionNode = $xml->createElement('question');
    
    if ($question['type'] == 'closed') {
        createClosedQuestion($xml, $questionNode, $question);
    } else {
        createOpenQuestion($xml, $questionNode, $question);
    }
    
    return $questionNode;
}

function createClosedQuestion($xml, $questionNode, $question) {
    $questionNode->setAttribute('type', 'multichoice');
    
    addQuestionName($xml, $questionNode, $question['question']);
    addQuestionText($xml, $questionNode, $question['question']);
    addAnswers($xml, $questionNode, $question['answers'], $question['correct_answer']);
    addQuestionSettings($xml, $questionNode);
}

function createOpenQuestion($xml, $questionNode, $question) {
    $questionNode->setAttribute('type', 'essay');
    
    addQuestionName($xml, $questionNode, $question['question']);
    addQuestionText($xml, $questionNode, $question['question']);
    addEmptyAnswer($xml, $questionNode);
}

function addQuestionName($xml, $questionNode, $questionText) {
    $name = $xml->createElement('name');
    $nameText = $xml->createElement('text', htmlspecialchars(substr($questionText, 0, 50) . '...'));
    $name->appendChild($nameText);
    $questionNode->appendChild($name);
}

function addQuestionText($xml, $questionNode, $questionText) {
    $questionTextElement = $xml->createElement('questiontext');
    $questionTextElement->setAttribute('format', 'html');
    
    $text = $xml->createElement('text');
    $cdata = $xml->createCDATASection($questionText);
    $text->appendChild($cdata);
    
    $questionTextElement->appendChild($text);
    $questionNode->appendChild($questionTextElement);
}

function addAnswers($xml, $questionNode, $answersStr, $correctAnswersStr) {
    $answers = explode(',', $answersStr);
    $correctAnswers = explode(',', $correctAnswersStr);

    foreach ($answers as $answer) {
        $answer = trim($answer);
        $answerNode = $xml->createElement('answer');
        
        $isCorrect = in_array($answer, $correctAnswers);
        $answerNode->setAttribute('fraction', $isCorrect ? '100' : '0');
        
        $answerText = $xml->createElement('text', htmlspecialchars($answer));
        $answerNode->appendChild($answerText);
        
        addFeedback($xml, $answerNode, $isCorrect);
        $questionNode->appendChild($answerNode);
    }
}

function addFeedback($xml, $answerNode, $isCorrect) {
    $feedback = $xml->createElement('feedback');
    $feedbackText = $xml->createElement('text', $isCorrect ? 'correct' : 'incorrect');
    $feedback->appendChild($feedbackText);
    $answerNode->appendChild($feedback);
}

function addQuestionSettings($xml, $questionNode) {
    $shuffle = $xml->createElement('shuffleanswers', '1');
    $questionNode->appendChild($shuffle);
    
    $single = $xml->createElement('single', 'true');
    $questionNode->appendChild($single);
    
    $numbering = $xml->createElement('answernumbering', 'abc');
    $questionNode->appendChild($numbering);
}

function addEmptyAnswer($xml, $questionNode) {
    $answer = $xml->createElement('answer');
    $answerText = $xml->createElement('text');
    $answer->appendChild($answerText);
    $answer->setAttribute('fraction', '0');
    $questionNode->appendChild($answer);
}

function outputXML($xml, $test_name) {
    header('Content-Type: application/xml');
    header('Content-Disposition: attachment; filename="moodle_export_' . $test_name . '.xml"');
    echo $xml->saveXML();
    exit;
}

// Main execution
check_auth_get(['id']);
$test_id = $_GET['id'];
exportTestToMoodleXML($test_id);
?>