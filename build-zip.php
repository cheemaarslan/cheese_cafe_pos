<?php
/**
 * build-zip.php
 *
 * Packs only the build files (public/js, public/css, public/mix-manifest.json)
 * that changed since the last deploy into build-update.zip, so one file can be
 * uploaded instead of many.
 *
 * Usage:
 *   php build-zip.php          Make build-update.zip with the changed files
 *   php build-zip.php --all    Put every build file in the zip
 *   php build-zip.php --done   After the zip is extracted on the server:
 *                              remember the current build as deployed
 *
 * On the server, in the folder where artisan is:
 *   unzip -o build-update.zip && rm build-update.zip
 *
 * Deployed hashes (md5) are kept in .build-zip-state.json. Forgetting --done is
 * harmless: the next zip just carries more files than it needs to.
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

const PROJECT_ROOT = __DIR__;
const STATE_FILE   = PROJECT_ROOT . '/.build-zip-state.json';
const ZIP_FILE     = PROJECT_ROOT . '/build-update.zip';

function fail(string $message): void
{
    fwrite(STDERR, "\n[build-zip] ERROR: {$message}\n");
    exit(1);
}

function info(string $message): void
{
    echo "[build-zip] {$message}\n";
}

// Every build file, relative to the project root. The manifest goes last so it
// is written after the files it points to.
function buildFiles(): array
{
    $files = [];
    foreach (['public/js', 'public/css'] as $dir) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(PROJECT_ROOT . '/' . $dir, FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $files[] = str_replace('\\', '/', substr($file->getPathname(), strlen(PROJECT_ROOT) + 1));
            }
        }
    }
    sort($files);
    $files[] = 'public/mix-manifest.json';

    return $files;
}

$args = array_slice($argv, 1);

if (!is_file(PROJECT_ROOT . '/public/mix-manifest.json')) {
    fail('public/mix-manifest.json not found. Run "npm run prod" first.');
}

$hashes = [];
foreach (buildFiles() as $path) {
    $hashes[$path] = md5_file(PROJECT_ROOT . '/' . $path);
}

if (in_array('--done', $args, true)) {
    file_put_contents(STATE_FILE, json_encode($hashes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    @unlink(ZIP_FILE);
    info('Saved: the current build is marked as deployed.');
    exit(0);
}

$deployed = [];
if (!in_array('--all', $args, true)) {
    if (is_file(STATE_FILE)) {
        $deployed = json_decode(file_get_contents(STATE_FILE), true) ?: [];
    } else {
        info('No ' . basename(STATE_FILE) . ' yet, so every build file goes in the zip.');
    }
}

$changed = array_keys(array_filter(
    $hashes,
    fn ($hash, $path) => ($deployed[$path] ?? null) !== $hash,
    ARRAY_FILTER_USE_BOTH
));

if (!$changed) {
    @unlink(ZIP_FILE);
    info('Nothing changed since the last deploy. No zip made.');
    exit(0);
}

if (!class_exists('ZipArchive')) {
    fail('The PHP zip extension is not enabled. Enable extension=zip in php.ini and retry.');
}

@unlink(ZIP_FILE);
$zip = new ZipArchive();
if ($zip->open(ZIP_FILE, ZipArchive::CREATE) !== true) {
    fail('Could not create ' . ZIP_FILE);
}
foreach ($changed as $path) {
    $zip->addFile(PROJECT_ROOT . '/' . $path, $path);
}
$zip->close();

foreach ($changed as $path) {
    echo "  {$path}\n";
}
info(count($changed) . ' file(s) packed into ' . basename(ZIP_FILE) . ' (' . round(filesize(ZIP_FILE) / 1024) . ' KB).');
echo "\nNext:\n";
echo "  1. Upload build-update.zip to the server (same folder as artisan).\n";
echo "  2. In the cPanel Terminal, in that folder:  unzip -o build-update.zip && rm build-update.zip\n";
echo "  3. Back here:  php build-zip.php --done\n";
