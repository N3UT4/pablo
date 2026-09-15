<?php
// Script temporal: agrega la cabecera PHP de Content-Type a los recursos JavaScript
// convertidos a .php para que el navegador los sirva como application/javascript.

$header = "<?php\n// Recurso JavaScript servido por PHP.\nheader('Content-Type: application/javascript; charset=utf-8');\n?>\n";

$archivos = glob(__DIR__ . '/*.php');
foreach ($archivos as $archivo) {
    $nombre = basename($archivo);
    if ($nombre === basename(__FILE__)) {
        continue; // No modificar este script.
    }

    $contenido = file_get_contents($archivo);
    if (strpos($contenido, '<?php') === 0) {
        echo "OMITIDO (ya tiene cabecera): $nombre\n";
        continue;
    }

    file_put_contents($archivo, $header . $contenido);
    echo "OK: $nombre\n";
}

echo "---LISTO---\n";
