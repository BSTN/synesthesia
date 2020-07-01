<!doctype html>
<html>

<head>
  <meta charset="utf-8" />
  <title>Synesthesia test</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="icon" type="image/png" href="assets/icon.png" />
</head>

<body>

  <style>
    html,
    body {
      background: #222;
      color: #fff;
    }

    * {
      font-size: 20px;
      font-family: helvetica, sans-serif;
    }

    form {
      margin: 2rem auto;
      max-width: 18rem;
      padding: 2rem;
    }

    form>* {
      display: block;
    }

    #message {
      background: #960000;
      padding: 1em;
      font-size: 0.75em;
      margin-bottom: 1em;
      border-radius: 0.5em;
    }

    form label {
      opacity: 0.5;
      font-size: 0.75em;
      padding: 0.5em 0;
    }

    form input {
      border: 0px;
      background: #000;
      color: #ccc;
      padding: 0.5em 0.5em;
      margin: 0 0 1rem;
      line-height: 1em;
      display: block;
      width: 100%;
      border-radius: 0.25em;
      outline: none;
    }

    button {
      line-height: 1em;
      background: #111;
      color: #666;
      border: 0;
      padding: 0.5em 2em;
      margin-top: 1em;
      display: block;
      border-radius: 0.25em;
    }

    button:hover {
      color: #fff;
      background: #000;
      cursor: pointer;
      ;
    }
  </style>

  <form method="POST" action="./download">
    <?php
    if ($message !== '') {
      echo "<div id='message'>$message</div>";
    };
    ?>
    <label>Username:</label>
    <input type="text" name="name" />
    <label>Password:</label>
    <input type="password" name="passwd" />
    <button type="submit">Submit</button>
  </form>

</body>

</html>