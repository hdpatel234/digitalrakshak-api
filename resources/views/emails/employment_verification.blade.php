<!DOCTYPE html>
<html>
<head>
    <title>Employment Verification Request</title>
</head>
<body>
    <p>Hello,</p>

    <p>You have received a request to verify the employment details of <strong>{{ $candidateName }}</strong>.</p>

    <p>Please click the button below to review the provided information and verify its accuracy.</p>

    <p>
        <a href="{{ $verificationUrl }}" style="display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">Verify Employment</a>
    </p>

    <p>If the button doesn't work, you can copy and paste the following link into your browser:</p>
    <p><a href="{{ $verificationUrl }}">{{ $verificationUrl }}</a></p>

    <p>Thank you,</p>
    <p>Digitalrakshak Team</p>
</body>
</html>
