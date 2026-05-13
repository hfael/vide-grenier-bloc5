<?php

require __DIR__ . '/TestRunner.php';
require dirname(__DIR__) . '/vendor/autoload.php';

use App\Utility\Hash;
use App\Utility\Upload;

$runner = new TestRunner();

$runner->test('Hash::generate produit un hash deterministe', function (TestRunner $test) {
    $hash = Hash::generate('secret', 'salt');
    $test->assertSame($hash, Hash::generate('secret', 'salt'));
    $test->assertSame(64, strlen($hash));
});

$runner->test('Hash::generateSalt retourne un salt de la taille demandee', function (TestRunner $test) {
    $salt = Hash::generateSalt(32);
    $test->assertSame(32, strlen($salt));
});

$runner->test('Upload::validateFile accepte une image JPEG valide', function (TestRunner $test) {
    $file = [
        'name' => 'annonce.JPG',
        'size' => 120000,
        'tmp_name' => '/tmp/fake'
    ];

    $test->assertTrue(Upload::validateFile($file));
});

$runner->test('Upload::validateFile refuse une extension non autorisee', function (TestRunner $test) {
    $test->expectException(function () {
        Upload::validateFile([
            'name' => 'script.php',
            'size' => 1000,
            'tmp_name' => '/tmp/fake'
        ]);
    });
});

$runner->test('Upload::validateFile refuse un fichier trop lourd', function (TestRunner $test) {
    $test->expectException(function () {
        Upload::validateFile([
            'name' => 'photo.png',
            'size' => 5000000,
            'tmp_name' => '/tmp/fake'
        ]);
    });
});

$runner->finish();
