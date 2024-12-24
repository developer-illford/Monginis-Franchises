<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Function to sanitize input data
    function sanitizeInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }

    // Sanitize inputs
    $applicationType = sanitizeInput($_POST['applicationType']);
    $cityName = sanitizeInput($_POST['CityName']);
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $countryCode = sanitizeInput($_POST['countryCode']);
    $mobile = sanitizeInput($_POST['mobile']);
    $message = sanitizeInput($_POST['message']);
    $investment = sanitizeInput($_POST['investment']);

    // Check honeypot field (must be empty if the form is genuine)
    if (!empty($_POST['honeypot'])) {
        die("Spam detected.");
    }

    // reCAPTCHA validation
    $recaptchaSecret = '6LefECgqAAAAANbAb-KSugRx_qNhxz0XbIH-4bE-';
    $recaptchaResponse = $_POST['g-recaptcha-response'];
    $recaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify';

    $response = file_get_contents($recaptchaUrl . '?secret=' . $recaptchaSecret . '&response=' . $recaptchaResponse);
    $responseKeys = json_decode($response, true);

    if (intval($responseKeys["success"]) !== 1) {
        die("Please verify that you're not a robot.");
    }

    // Proceed with form processing
    $to = "support@monginisfranchises.org.in, shivamsundram125@gmail.com";
    $bcc = "shivamsundram125@gmail.com";
    $subject = "Monginis New Enquiry Form $name";
    $messageBody = "Application Type: $applicationType\nCity Name: $cityName\nName: $name\nEmail: $email\nPhone: $countryCode $mobile\nMessage: $message\nInvestment: $investment";
    $headers = "From: noreply@monginisfranchises.org.in\r\n";
    $headers .= "Bcc: $bcc\r\n";

    if (mail($to, $subject, $messageBody, $headers)) {
        // Send auto-reply to the user
        $userSubject = "Thank you for contacting Monginis";
        $userMessage = "Dear $name,\n\nThank you for reaching out to us. We have received your enquiry and will get back to you shortly.\n\nBest Regards,\nMonginis Franchises Team";
        $userHeaders = "From: noreply@monginisfranchises.org.in";

        mail($email, $userSubject, $userMessage, $userHeaders);

        echo "<script>alert('Thank you! Your submission has been received.'); window.location.href = 'index.html';</script>";
    } else {
        echo "<script>alert('Sorry, there was an error sending your message. Please try again later.'); window.location.href = 'index.html';</script>";
    }
}
?>
