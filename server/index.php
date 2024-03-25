<?php
include './config.php';
include './api/api-functions.php';
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Synesthesia test</title>
    <base href="<?= BASE ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" type="image/png" href="assets/96x96.png" />
</head>

<body data-configbase="<?= CONFIGBASE ?>">
    <div id='container'></div>
    <div id="connected"></div>

    <?php
    // load composer modules
    require_once __DIR__ . "/vendor/autoload.php";

use Symfony\Component\Yaml\Yaml;
use Michelf\Markdown;

// load yaml configuration
$raw = file_get_contents(CONFIGPATH . '/config.yml');
$config = Yaml::parse($raw);
echo "\t<script type=\"application/json\" id=\"bootload-config\">" . json_encode($config) . "</script>\n\t";

// load yaml translations
$raw = file_get_contents(CONFIGPATH . '/translations.yml');
$config = Yaml::parse($raw);
echo "\t<script type=\"application/json\" id=\"bootload-translations\">" . json_encode($config) . "</script>\n\t";

// load yaml tests
$tests = array();
foreach (glob(CONFIGPATH . "/tests/*.yml") as $filename) {
    $raw = file_get_contents($filename);
    $yaml = Yaml::parse($raw);
    $name = pathinfo($filename, PATHINFO_FILENAME);
    $tests[$name] = $yaml;
}
echo "\t<script type=\"application/json\" id=\"bootload-tests\">" . json_encode($tests) . "</script>\n\t";

// load all markdown texts
foreach (glob(CONFIGPATH . "/texts/*.md") as $filename) {
    $raw = file_get_contents($filename);
    $md = Markdown::defaultTransform($raw);
    $md = unwrap($md);
    $name = pathinfo($filename, PATHINFO_FILENAME);
    echo "\t<script type=\"text/template\" id=\"template$name\">\n\t<div id=\"template\">$md</div>\n\t</script>\n\n";
}
?>

    <script src="<?= SRCV ?>"></script>
    <script src="<?= SRC ?>"></script>
    
</body>

</html>