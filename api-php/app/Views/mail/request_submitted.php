<!DOCTYPE html>
<html>
<head>
  <title>Finish Voting Register Form</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f1f1f1;
      margin: 0;
      padding: 100px;
    }

    .container {
      max-width: 600px;
      margin: 0 auto;
      padding: 80px;
      background-color: #fff;
      border: 1px solid #ccc;
      border-radius: 5px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h1 {
      text-align: center;
      margin-bottom: 50px;
    }

    p {
      margin-top: 20px;
      text-align: center;
    }

    .next-step {
      text-align: center;
      margin-top: 80px;
    }

    .next-step a {
      display: inline-block;
      padding: 10px 20px;
      background-color: #4CAF50;
      color: #fff;
      text-decoration: none;
      border-radius: 5px;
    }

    .next-step a:hover {
      background-color: #45a049;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Voting form submitted!</h1>
    <p>Thank you for completing the voting register form.</p>
    <p>Your registration has been successfully recorded.</p>
    <div class="next-step">
      <a href="<?= $link ?>">Proceed to Next Step</a>
    </div>
  </div>
</body>
</html>