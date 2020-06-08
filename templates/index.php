<!doctype html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>Synesthesia test</title>
    <base href="<%= BASE %>">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" type="image/png" href="assets/icon.png" />
  </head>
  <body>
    <div id='container'>
    </div>
    <script src="<%= SRCV %>"></script>
    <script src="<%= SRC %>"></script>
    <script src="assets/paper.js"></script>
    <script type="text/paperscript" canvas="canvas-1" src="assets/connected.js?<?= time();?>"></script>
    <canvas resize="true" id="canvas-1" ></canvas>

    <?php
    require_once __DIR__ .  "/vendor/autoload.php";
    use Symfony\Component\Yaml\Yaml;
    $value = Yaml::parse("foo: bar");
    print_r($value);

    use Michelf\Markdown;
    echo Markdown::defaultTransform('#hello');
    ?>
  </body>
</html>