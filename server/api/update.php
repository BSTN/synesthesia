<?php

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

function download_config_archive($targetZipPath)
{
    if (!function_exists('curl_init')) {
        throw new RuntimeException('cURL extension is required for config updates.');
    }

    $ch = curl_init(CONFIG_REPO_ARCHIVE_URL);
    $fp = fopen($targetZipPath, 'w');
    if ($fp === false) {
        throw new RuntimeException('Could not create temporary archive file.');
    }

    curl_setopt_array($ch, array(
        CURLOPT_FILE => $fp,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 120,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_FAILONERROR => false,
        CURLOPT_USERAGENT => 'synesthesia-config-updater/1.0',
    ));

    $success = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    fclose($fp);

    if ($success === false) {
        @unlink($targetZipPath);
        throw new RuntimeException('Archive download failed: ' . $error);
    }

    if ($status < 200 || $status >= 300) {
        @unlink($targetZipPath);
        throw new RuntimeException('Archive download failed with HTTP ' . $status . '.');
    }
}

function extract_config_archive($zipPath, $extractPath)
{
    if (!class_exists('ZipArchive')) {
        throw new RuntimeException('ZipArchive extension is required for config updates.');
    }

    $zip = new ZipArchive();
    if ($zip->open($zipPath) !== true) {
        throw new RuntimeException('Could not open downloaded archive.');
    }

    ensure_directory($extractPath);
    if (!$zip->extractTo($extractPath)) {
        $zip->close();
        throw new RuntimeException('Could not extract downloaded archive.');
    }
    $zip->close();
}

function find_extracted_config_root($extractPath)
{
    $entries = glob(join_paths($extractPath, '*'));
    foreach ($entries as $entry) {
        if (is_dir($entry) && is_file(join_paths($entry, 'config.yml')) && is_dir(join_paths($entry, 'tests'))) {
            return $entry;
        }
    }

    throw new RuntimeException('Extracted archive did not contain the expected config repository structure.');
}

function config_validation_error($file, $message)
{
    return array(
        'file' => $file,
        'message' => $message,
    );
}

function validate_yaml_file($rootPath, $path)
{
    $relative = ltrim(str_replace($rootPath, '', $path), DIRECTORY_SEPARATOR);

    try {
        return array(
            'data' => Yaml::parseFile($path),
            'errors' => array(),
        );
    } catch (ParseException $e) {
        return array(
            'data' => null,
            'errors' => array(
                config_validation_error($relative, $e->getMessage()),
            ),
        );
    }
}

function validate_test_structure($rootPath, $relative, $data)
{
    $errors = array();

    if (!is_array($data)) {
        $errors[] = config_validation_error($relative, 'Test file must parse to a mapping/object.');
        return $errors;
    }

    foreach (array('name', 'type', 'selector', 'cutoff', 'help', 'sets') as $required) {
        if (!array_key_exists($required, $data)) {
            $errors[] = config_validation_error($relative, 'Missing required key "' . $required . '".');
        }
    }

    if (array_key_exists('name', $data) && !is_array($data['name'])) {
        $errors[] = config_validation_error($relative, 'Key "name" must be a mapping of language codes.');
    }

    if (array_key_exists('help', $data) && !is_array($data['help'])) {
        $errors[] = config_validation_error($relative, 'Key "help" must be a mapping of language codes.');
    }

    if (array_key_exists('sets', $data) && !is_array($data['sets'])) {
        $errors[] = config_validation_error($relative, 'Key "sets" must be a mapping of named question sets.');
    }

    return $errors;
}

function validate_config_repository($path)
{
    $errors = array();

    $requiredFiles = array(
        'config.yml',
        'translations.yml',
    );

    foreach ($requiredFiles as $file) {
        $fullPath = join_paths($path, $file);
        if (!is_file($fullPath)) {
            $errors[] = config_validation_error($file, 'Required file is missing.');
        }
    }

    foreach (array('tests', 'texts') as $dir) {
        $fullPath = join_paths($path, $dir);
        if (!is_dir($fullPath)) {
            $errors[] = config_validation_error($dir, 'Required directory is missing.');
        }
    }

    if ($errors) {
        return $errors;
    }

    $configValidation = validate_yaml_file($path, join_paths($path, 'config.yml'));
    $errors = array_merge($errors, $configValidation['errors']);
    $configData = $configValidation['data'];
    if (is_array($configData)) {
        foreach (array('languages', 'defaultLanguage') as $required) {
            if (!array_key_exists($required, $configData)) {
                $errors[] = config_validation_error('config.yml', 'Missing required key "' . $required . '".');
            }
        }
        if (array_key_exists('languages', $configData) && !is_array($configData['languages'])) {
            $errors[] = config_validation_error('config.yml', 'Key "languages" must be a mapping.');
        }
    }

    $translationsValidation = validate_yaml_file($path, join_paths($path, 'translations.yml'));
    $errors = array_merge($errors, $translationsValidation['errors']);
    if ($translationsValidation['data'] !== null && !is_array($translationsValidation['data'])) {
        $errors[] = config_validation_error('translations.yml', 'Translations file must parse to a mapping/object.');
    }

    foreach (glob(join_paths($path, 'tests', '*.yml')) as $testFile) {
        $relative = ltrim(str_replace($path, '', $testFile), DIRECTORY_SEPARATOR);
        $validation = validate_yaml_file($path, $testFile);
        $errors = array_merge($errors, $validation['errors']);
        if ($validation['data'] !== null) {
            $errors = array_merge($errors, validate_test_structure($path, $relative, $validation['data']));
        }
    }

    foreach (glob(join_paths($path, 'texts', '*.md')) as $markdownFile) {
        $relative = ltrim(str_replace($path, '', $markdownFile), DIRECTORY_SEPARATOR);
        if (!is_readable($markdownFile)) {
            $errors[] = config_validation_error($relative, 'Markdown file is not readable.');
            continue;
        }

        $contents = file_get_contents($markdownFile);
        if ($contents === false) {
            $errors[] = config_validation_error($relative, 'Markdown file could not be read.');
        }
    }

    return $errors;
}

function replace_directory_atomically($sourcePath, $targetPath)
{
    $parent = dirname($targetPath);
    ensure_directory($parent);

    $backupPath = $targetPath . '.backup-' . gmdate('YmdHis');

    if (is_dir($targetPath)) {
        if (!rename($targetPath, $backupPath)) {
            throw new RuntimeException('Could not move existing config directory out of the way.');
        }
    } else {
        $backupPath = null;
    }

    try {
        if (!rename($sourcePath, $targetPath)) {
            throw new RuntimeException('Could not replace config directory with validated update.');
        }
    } catch (Throwable $e) {
        if ($backupPath && is_dir($backupPath) && !is_dir($targetPath)) {
            rename($backupPath, $targetPath);
        }
        throw $e;
    }

    if ($backupPath && is_dir($backupPath)) {
        rrmdir($backupPath);
    }
}

function perform_config_update()
{
    ensure_directory(TEMP_PATH);
    $workRoot = join_paths(TEMP_PATH, 'config-update-' . bin2hex(random_bytes(6)));
    $archivePath = join_paths($workRoot, 'config.zip');
    $extractPath = join_paths($workRoot, 'extracted');
    ensure_directory($workRoot);

    try {
        download_config_archive($archivePath);
        extract_config_archive($archivePath, $extractPath);
        $clonePath = find_extracted_config_root($extractPath);

        $errors = validate_config_repository($clonePath);
        if ($errors) {
            return array(
                'status' => 'validation_failed',
                'repository' => CONFIG_REPO_ARCHIVE_URL,
                'branch' => CONFIG_REPO_BRANCH,
                'errors' => $errors,
            );
        }

        replace_directory_atomically($clonePath, CONFIGPATH);

        return array(
            'status' => 'success',
            'repository' => CONFIG_REPO_ARCHIVE_URL,
            'branch' => CONFIG_REPO_BRANCH,
            'path' => CONFIGPATH,
            'message' => 'Configuration repository updated successfully.',
        );
    } finally {
        if (isset($archivePath) && is_file($archivePath)) {
            unlink($archivePath);
        }
        if (isset($extractPath) && is_dir($extractPath)) {
            rrmdir($extractPath);
        }
        if (is_dir($workRoot)) {
            rrmdir($workRoot);
        }
    }
}
