#!/usr/bin/env php
<?php
require __DIR__ . '/vendor/autoload.php';
$binFile = "nginxgenerator";
$pharName = "nginx-generators.phar";
$pharFile = __DIR__ . '/' . $pharName;
if (file_exists($pharFile)) {
    unlink($pharFile);
}
$phar = new \Phar($pharFile, 0, $pharName);
$phar->setSignatureAlgorithm(\Phar::SHA1);
$phar->startBuffering();
$finder = new Symfony\Component\Finder\Finder();
$finder->files()
    ->ignoreVCS(true)
    ->name('*.php')
    ->notName('build.php')
    ->exclude('phpunit')
    ->exclude('Tests')
    ->exclude('test')
    ->exclude('tests')
    ->exclude('phpspec')
    ->in(__DIR__);
foreach ($finder as $fileInfo) {
    $file = str_replace(__DIR__, '', $fileInfo->getRealPath());
    echo "Add file: " . $file . "\n";
    $phar->addFile($fileInfo->getRealPath(), $file);
}
// Add bin/dep file
$depContent = file_get_contents(__DIR__ . '/bin/' . $binFile);
$depContent = str_replace("#!/usr/bin/env php\n", '', $depContent);
$depContent = str_replace('__FILE__', 'str_replace("phar://", "", Phar::running())', $depContent);
$phar->addFromString('bin/' . $binFile, $depContent);
$stub = <<<STUB
#!/usr/bin/env php
<?php
Phar::mapPhar('{$pharName}');
require 'phar://{$pharName}/bin/' . $binFile;
__HALT_COMPILER();
STUB;
$phar->setStub($stub);
$phar->compressFiles(Phar::GZ);
$phar->stopBuffering();
unset($phar);
echo "$pharName was created successfully.\n";
