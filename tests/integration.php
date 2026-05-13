<?php

require __DIR__ . '/TestRunner.php';

$runner = new TestRunner();
$baseUrl = rtrim(getenv('APP_BASE_URL') ?: 'http://localhost:8080', '/');

function http_request($method, $path, $body = null)
{
    global $baseUrl;

    $headers = [];
    $content = null;

    if ($body !== null) {
        $content = is_array($body) ? http_build_query($body) : $body;
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    }

    $context = stream_context_create([
        'http' => [
            'method' => $method,
            'header' => implode("\r\n", $headers),
            'content' => $content,
            'ignore_errors' => true,
            'timeout' => 15
        ]
    ]);

    $responseBody = file_get_contents($baseUrl . $path, false, $context);
    $statusLine = $http_response_header[0] ?? 'HTTP/1.1 0';
    preg_match('/\s(\d{3})\s/', $statusLine, $matches);

    return [
        'status' => isset($matches[1]) ? (int) $matches[1] : 0,
        'body' => $responseBody ?: ''
    ];
}

$runner->test('API produits retourne du JSON exploitable', function (TestRunner $test) {
    $response = http_request('GET', '/api/products?sort=date');
    $test->assertSame(200, $response['status']);

    $products = json_decode($response['body'], true);
    $test->assertTrue(is_array($products), 'La reponse API doit etre un tableau JSON.');
    $test->assertTrue(count($products) > 0, 'Le jeu de donnees doit contenir des annonces.');
    $test->assertTrue(isset($products[0]['id'], $products[0]['name']), 'Une annonce doit contenir id et name.');
});

$runner->test('Fiche produit affiche le formulaire de contact sans ouvrir de mailto', function (TestRunner $test) {
    $response = http_request('GET', '/product/1');
    $test->assertSame(200, $response['status']);
    $test->assertContains('name="contact_submit"', $response['body']);
    $test->assertNotContains('mailto:', $response['body']);
});

$runner->test('Soumission du formulaire de contact reste sur la fiche produit', function (TestRunner $test) {
    $response = http_request('POST', '/product/1', [
        'contact_submit' => '1',
        'contact_name' => 'Client test',
        'contact_email' => 'client@example.test',
        'contact_message' => 'Bonjour, votre annonce est-elle toujours disponible ?'
    ]);

    $test->assertSame(200, $response['status']);
    $test->assertContains('Votre message a bien', $response['body']);
});

$runner->finish();
