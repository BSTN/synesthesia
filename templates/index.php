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
    <div id='container'></div>

    <?php
    // load composer modules
    require_once __DIR__ .  "/vendor/autoload.php";
    use Symfony\Component\Yaml\Yaml;
    use Michelf\Markdown;
    
    // load yaml settings
    $raw = file_get_contents('./data/config.yml');
    $config = Yaml::parse($raw);
    echo "<script type=\"application/json\" id=\"bootload-config\">" . json_encode($config) . "</script>\n\t";

    // load yaml tests
    $tests = array();
    foreach (glob("./data/tests/*.yml") as $filename) {
      $raw = file_get_contents($filename);
      $yaml = Yaml::parse($raw);
      $name = pathinfo($filename, PATHINFO_FILENAME);
      $tests[$name] = $yaml;
    }
    echo "<script type=\"application/json\" id=\"bootload-tests\">" . json_encode($tests) . "</script>\n\t";

    // load all markdown
    foreach (glob("./data/texts/*.md") as $filename) {
      $raw = file_get_contents($filename);
      $md = Markdown::defaultTransform($raw);
      $name = pathinfo($filename, PATHINFO_FILENAME);
      echo "\t<script type=\"text/template\" id=\"template$name\">\n\t<div id=\"md\">$md</div>\n\t</script>\n\n";
    }
    ?>
    
    <script src="<%= SRCV %>"></script>
    <script src="<%= SRC %>"></script>
    <script src="assets/paper.js"></script>
    <script type="text/paperscript" canvas="canvas-1" src="assets/connected.js?<?= time();?>"></script>
    <canvas resize="true" id="canvas-1" ></canvas>

  </body>
</html>