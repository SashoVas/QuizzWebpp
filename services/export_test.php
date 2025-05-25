<?php
require __DIR__ . '/../database/db.php';
require __DIR__ . '/../helpers/auth_helpers.php';

check_auth_get(['id']);

$test_id = $_GET['id'];

//get data from db
$stmt = $pdo->prepare("SELECT * FROM tests WHERE id = ?");
$stmt->execute([$test_id]);
$test = $stmt->fetch();
if (!$test) {
    header("Location: ../pages/main.php?message=error&error=bad_request");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM questions WHERE test_id = ? ORDER BY id");
$stmt->execute([$test_id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!$questions) {
    header("Location: ../pages/main.php?message=error&error=bad_request");
    exit;
}

//generate moodle xml
$xml = new DOMDocument('1.0', 'UTF-8');
$xml->formatOutput = true;

// create root element
$quiz = $xml->createElement('quiz');
$xml->appendChild($quiz);

//add category
$categoryQuestion = $xml->createElement('question');
$categoryQuestion->setAttribute('type', 'category');
$category = $xml->createElement('category');
$categoryText = $xml->createElement('text', '$course$/' . htmlspecialchars($test['name']));
$category->appendChild($categoryText);
$categoryQuestion->appendChild($category);
$quiz->appendChild($categoryQuestion);

//loop through questions
foreach ($questions as $question) {
    $questionNode = $xml->createElement('question');
    
    if ($question['type'] == 'closed') {
        $questionNode->setAttribute('type', 'multichoice');
        
        //question name
        $name = $xml->createElement('name');
        $nameText = $xml->createElement('text', htmlspecialchars(substr($question['question'], 0, 50) . '...'));
        $name->appendChild($nameText);
        $questionNode->appendChild($name);
        
        //question text
        $questionText = $xml->createElement('questiontext');
        $questionText->setAttribute('format', 'html');
        $text = $xml->createElement('text');
        $cdata = $xml->createCDATASection($question['question']);
        $text->appendChild($cdata);
        $questionText->appendChild($text);
        $questionNode->appendChild($questionText);
        
        //answers
        $answers = explode(',', $question['answers']);
        $correctAnswers = explode(',', $question['correct_answer']);

        foreach ($answers as $answer) {
            $answer = trim($answer);
            $answerNode = $xml->createElement('answer');
            
            //check if this answer is correct
            $isCorrect = in_array($answer, $correctAnswers);
            $answerNode->setAttribute('fraction', $isCorrect ? '100' : '0');
            
            $answerText = $xml->createElement('text', htmlspecialchars($answer));
            $answerNode->appendChild($answerText);
            
            //feedback
            $feedback = $xml->createElement('feedback');
            $feedbackText = $xml->createElement('text', $isCorrect ? 'correct' : 'incorrect');
            $feedback->appendChild($feedbackText);
            $answerNode->appendChild($feedback);
            
            $questionNode->appendChild($answerNode);
        }
        
        //add question settings
        $shuffle = $xml->createElement('shuffleanswers', '1');
        $questionNode->appendChild($shuffle);
        
        $single = $xml->createElement('single', 'true');
        $questionNode->appendChild($single);
        
        $numbering = $xml->createElement('answernumbering', 'abc');
        $questionNode->appendChild($numbering);
        
    } else { //open question type
        $questionNode->setAttribute('type', 'essay');
        
        //question name
        $name = $xml->createElement('name');
        $nameText = $xml->createElement('text', htmlspecialchars(substr($question['question'], 0, 50) . '...'));
        $name->appendChild($nameText);
        $questionNode->appendChild($name);
        
        //question text
        $questionText = $xml->createElement('questiontext');
        $questionText->setAttribute('format', 'html');
        $text = $xml->createElement('text');
        $cdata = $xml->createCDATASection($question['question']);
        $text->appendChild($cdata);
        $questionText->appendChild($text);
        $questionNode->appendChild($questionText);
        
        //empty answer for essay type
        $answer = $xml->createElement('answer');
        $answerText = $xml->createElement('text');
        $answer->appendChild($answerText);
        $answer->setAttribute('fraction', '0');
        $questionNode->appendChild($answer);
    }
    
    $quiz->appendChild($questionNode);
}

//output the xml
header('Content-Type: application/xml');
header('Content-Disposition: attachment; filename="moodle_export_' . $test['name'] . '.xml"');
echo $xml->saveXML();
exit;
?>