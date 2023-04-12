<?php

// Check if at least one field is not empty
if (strlen($_POST['firstName']) > 0 ||
    strlen($_POST['lastName']) > 0 ||
    strlen($_POST['phoneNumber']) > 0 ||
    strlen($_POST['emailAddress']) > 0 ||
    strlen($_POST['enquiryMessage']) > 0) {

    ////////////////////////////////////
    // At least one field is not empty//
    ////////////////////////////////////

    // Check if firstName field is empty or invalid
    $firstName = $_POST['firstName'];
    $firstName_regex = "/^[A-Za-z .'-]+$/";
    if (!preg_match($firstName_regex, $firstName)) {
        echo "<script type='text/javascript'>alert('The first name is missing or does not appear to be valid. Please try again');window.location.href='/index.html';</script>";
        exit();
    }

    // Check if lastName field is empty or invalid
    $lastName = $_POST['lastName'];
    $lastName_regex = "/^[A-Za-z .'-]+$/";
    if (!preg_match($lastName_regex, $lastName)) {
        echo "<script type='text/javascript'>alert('The last name is missing or does not appear to be valid.  Please try again');window.location.href='/index.html';</script>";
        exit();
    }

    // Check if phoneNumber field is empty or is invalid
    $phoneNumber = $_POST['phoneNumber'];
    $phoneNumber_regex = '/^[0-9]+$/';
    if (!preg_match($phoneNumber_regex, $phoneNumber) || substr($phoneNumber, 0, 2) != '07' || strlen($phoneNumber) != 11) {
        echo "<script type='text/javascript'>alert('The phone number is missing or does not appear to be a valid mobile phone number. Please try again');window.location.href='/index.html';</script>";
        exit();
    }

    // Check if emailAddress field is empty or is invalid
    $emailAddress = $_POST['emailAddress'];
    $emailAddress_regex = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
    if (!preg_match($emailAddress_regex, $emailAddress)) {
        echo "<script type='text/javascript'>alert('The email address is missing or does not appear to be valid. Please try again');window.location.href='/index.html';</script>";
        exit();
    }

    // Check if enquiryMessage field is empty
    $enquiryMessage = $_POST['enquiryMessage'];
    if (strlen($enquiryMessage) == 0) {
        echo "<script type='text/javascript'>alert('Your message is missing. Please try again');window.location.href='/index.html';</script>";
        exit();
    }

    //////////////////////////////////
    // Buid SMS message and send it //
    //////////////////////////////////

    // Base URL and send PHP script
    $url = "https://api-mapper.clicksend.com/http/v2/send.php";
        
    // Build sender ID for SMS message
    $senderid = "Unknown";

    // Format phone number correctly
    $phoneNumber = '+44' . substr($phoneNumber, 1);
    
    // Build SMS message body
    $SMSMessage = "You have a message from a potential client. Here are the details.". "\n\n";
    $SMSMessage .= "First Name: " . $firstName . "\n";
    $SMSMessage .= "Last Name: " . $lastName . "\n";
    $SMSMessage .= "Email Address: " . $emailAddress . "\n";
    $SMSMessage .= "Phone Number: " . $phoneNumber . "\n";
    $SMSMessage .= "Enquiry Message: " . $enquiryMessage;
    
    // Build array for API call
    $recipientPhoneNumber = "447971818756";
    // $recipientPhoneNumber = "447757782537";
    $data = array("username" => "stephen.j.learmonth@gmail.com", "key" => "8C32B75C-35A6-C906-5A04-A6CD05141B11", "to" => $recipientPhoneNumber, "senderid" => $senderid, "message" => $SMSMessage);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Send the SMS message
    $response = curl_exec($ch);

    // Close the connection
    curl_close($ch);

    // check that SMS message has been sent successfully
    if (strpos($response, "Success") == false) {
        $SMSSentSuccessfully = false;
    } else {
        $SMSSentSuccessfully = true;
    }

    ////////////////////////////////////
    // Buid email message and send it //
    ////////////////////////////////////

    // Build email addresses of recipients
    $emailTo = 'jamespgunderwood@hotmail.com';
    // $emailTo = 'stephen.j.learmonth@gmail.com';
    $emailDev = 'jamespgunderwoodenquiries@gmail.com';

    // Build email subject
    $emailSubject = "You have a physiotherapy enquiry!";
    
    // Build the email body
    $emailMessage = "You have a message from a potential client. Here are the details.". "<br /><br />";
    $emailMessage .= "First Name: " . $firstName . "<br /><br />";
    $emailMessage .= "Last Name: " . $lastName . "<br /><br />";
    $emailMessage .= "Email Address: " . $emailAddress . "<br /><br />";
    $emailMessage .= "Phone Number: " . $phoneNumber . "<br /><br />";
    $emailMessage .= "Enquiry Message: " . $enquiryMessage;
 
    // Build email headers
    $headers = "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-Type: text/html; charset=iso-8859-1";
    
    // Send the email messages
    $emailSentSuccessfully = mail($emailTo, $emailSubject, $emailMessage, $headers) &&
                             mail($emailDev, $emailSubject, $emailMessage, $headers);

    // Check if both the SMS and mail messages have been sent successfully
    if ($SMSSentSuccessfully && $emailSentSuccessfully) {
            echo "<script type='text/javascript'>alert('Thank you. Your message has been sent.');window.location.href='/index.html';</script>";
        } else {
            echo "<script type='text/javascript'>alert('There was a problem sending your message. Please try again');window.location.href='/index.html';</script>";
        }    

} else {
    
    // All fields are empty so display error message to user
    echo "<script type='text/javascript'>alert('All fields are empty. Please try again.');window.location.href='/index.html';</script>";            
}
?>
