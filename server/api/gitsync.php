<!doctype html>
<html>

<head>
  <meta charset="utf-8" />
  <title>Synesthesia Sync Git Configuration</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="icon" type="image/png" href="assets/icon.png" />
</head>

<body>

  <style><?php include_once("form.css");?></style>

  <form method="POST" action="./update">
    <?php
    include_once('../config.php');
    if ($message !== '') {
      echo "<div id='message'>$message</div>";
    };
    ?>
    <h1>Update configuration from github</h1>
    <a href="https://github.com/<?= GITNAME . "/" . GITREPOSITORY ?>" target="_blank"><?= GITNAME . "/" . GITREPOSITORY ?></a>
    <label>Username:</label>
    <input type="text" name="name" />
    <label>Password:</label>
    <input type="password" name="passwd" />
    <button type="submit">Submit</button>
  </form>

</body>

</html>