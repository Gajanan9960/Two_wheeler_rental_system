<?php
function sendEmail($to, $subject, $message) {
    // Mock Email System: Logs email to a file
    $logFile = __DIR__ . '/../logs/emails.txt';
    
    $timestamp = date("Y-m-d H:i:s");
    $emailContent = "--------------------------------------------------\n";
    $emailContent .= "Time: $timestamp\n";
    $emailContent .= "To: $to\n";
    $emailContent .= "Subject: $subject\n";
    $emailContent .= "Message:\n$message\n";
    $emailContent .= "--------------------------------------------------\n\n";
    
    // Create logs directory if it doesn't exist
    if (!file_exists(dirname($logFile))) {
        mkdir(dirname($logFile), 0777, true);
    }

    file_put_contents($logFile, $emailContent, FILE_APPEND);
    return true;
}
?>
