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
    <h1>Ballot successfully generated! </h1>
    <p>Thank you for successfully completing the necessary steps. You are now ready to cast your vote in the voting section using the provided EVUID and the signature of the Central Authority.</p>

    <div>
      <p>EVUID: <?= $evuid ?></p>
      <p>Signature: <?= $signature ?></p>
    </div>

    <div class="next-step">
      <a href="<?= $link ?>">START VOTE</a>
    </div>
  </div>
</body>
</html>