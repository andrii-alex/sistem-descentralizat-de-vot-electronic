<!DOCTYPE html>
<html>
<head>
    <title>Email Validation</title>
    <style>
        body {
            background-color: #f1f1f1;
            font-family: Arial, sans-serif;
            text-align: center;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 40px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333333;
        }

        p {
            margin-bottom: 20px;
            color: #666666;
        }

        .code {
            display: inline-block;
            padding: 10px 20px;
            background-color: #ee6e73;
            color: #ffffff;
            font-size: 24px;
            border-radius: 6px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Email Validation</h1>
        <p>Thank you for signing up! Your verification code is:</p>
        <p class="code"><?php echo $code; ?></p>
    </div>
</body>
</html>