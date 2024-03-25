<!doctype html>
<html>

<head>
  <meta charset="utf-8" />
  <title>Synesthesia Download</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="icon" type="image/png" href="/assets/icon.png" />
</head>

<body>

  <style><?php include_once("form.css");?></style>

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