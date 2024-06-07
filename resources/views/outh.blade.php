<?php
// Ensure you have received the authorization code
if (!isset($_GET['code'])) {
    die('Error: Authorization code not found.');
}

// Get the authorization code from the query parameters
$authCode = $_GET['code'];

// Set your client credentials and redirect URI
$client_id = getenv('GOOGLE_CLIENT_ID_d');
$client_secret = getenv('GOOGLE_CLIENT_SECRET_d');
$redirect_uri = 'http://localhost/expel_app/public/outh';
$token_url = 'https://oauth2.googleapis.com/token';

// Prepare the POST fields
$post_fields = [
    'code' => $authCode,
    'client_id' => $client_id,
    'client_secret' => $client_secret,
    'redirect_uri' => $redirect_uri,
    'grant_type' => 'authorization_code',
];

// Initialize cURL
$ch = curl_init();

// Set the cURL options
curl_setopt($ch, CURLOPT_URL, $token_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded'
]);

// Execute the cURL request and get the response
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    die('Curl error: ' . curl_error($ch));
}

// Close the cURL session
curl_close($ch);

// Decode the JSON response
$response_data = json_decode($response, true);

// Check if the response contains an error
if (isset($response_data['error'])) {
    die('Error fetching the tokens: ' . $response_data['error']);
}

// Extract the access token and refresh token
$accessToken = $response_data['access_token'];
$refreshToken = $response_data['refresh_token'];

echo "Access Token: " . $accessToken . "<br>";
echo "Refresh Token: " . $refreshToken . "<br>";

// Optionally, you can save the tokens to a session or database for later use
// For demonstration, we'll just display the user info

// Make an authorized API request using the access token
$userInfoUrl = 'https://www.googleapis.com/oauth2/v1/userinfo?alt=json&access_token=' . urlencode($accessToken);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $userInfoUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$userInfoResponse = curl_exec($ch);

if (curl_errno($ch)) {
    die('Curl error: ' . curl_error($ch));
}

curl_close($ch);

// Decode and display the user info
$userInfo = json_decode($userInfoResponse, true);
echo "User Info: <pre>" . print_r($userInfo, true) . "</pre>";
?>

<!-- https://accounts.google.com/o/oauth2/auth?client_id=990392568575-jitq4hirb3vgph8evn1rsea9fhj3g33b.apps.googleusercontent.com&redirect_uri=http%3A%2F%2Flocalhost%2Fexpel_app%2Fpublic%2Fouth&response_type=code&scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Ffirebase.messaging&access_type=offline&prompt=consent&state=firebase_messaging -->