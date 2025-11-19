<?php

/**
 * API del Clima para Santander, Cantabria
 * Consume la API de OpenWeatherMap y retorna datos en formato JSON
 */

// Configurar headers para JSON
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

/**
 * Carga las variables de entorno desde el archivo .env
 */
function cargarVariablesEntorno()
{
    $rutaArchivo = __DIR__ . '/.env';

    if (!file_exists($rutaArchivo)) {
        return false;
    }

    $lineas = file($rutaArchivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lineas as $linea) {
        // Ignorar comentarios
        if (strpos(trim($linea), '#') === 0) {
            continue;
        }

        // Separar clave y valor
        list($clave, $valor) = explode('=', $linea, 2);
        $clave = trim($clave);
        $valor = trim($valor);

        // Asignar variable de entorno
        $_ENV[$clave] = $valor;
        putenv("$clave=$valor");
    }

    return true;
}

/**
 * Obtiene datos del clima desde la API de OpenWeatherMap
 */
function obtenerDatosClima($apiKey, $ciudad, $pais, $idioma)
{
    $ubicacion = urlencode($ciudad . ',' . $pais);
    $url = "http://api.openweathermap.org/data/2.5/weather?q={$ubicacion}&appid={$apiKey}&units=metric&lang={$idioma}";

    // Configurar contexto para la peticion HTTP
    $contexto = stream_context_create([
        'http' => [
            'timeout' => 10,
            'method' => 'GET',
            'header' => 'User-Agent: Aplicacion Clima PHP/1.0'
        ]
    ]);

    // Realizar peticion HTTP
    $respuesta = @file_get_contents($url, false, $contexto);

    if ($respuesta === false) {
        return null;
    }

    return json_decode($respuesta, true);
}

/**
 * Procesa los datos recibidos de la API
 */
function procesarDatosClima($datos)
{
    if (!$datos || !isset($datos['main']) || !isset($datos['name'])) {
        return null;
    }

    return [
        'temperatura' => $datos['main']['temp'],
        'ciudad' => $datos['name'],
        'pais' => $datos['sys']['country'] ?? 'ES',
        'descripcion' => $datos['weather'][0]['description'] ?? '',
        'humedad' => $datos['main']['humidity'] ?? 0,
        'presion' => $datos['main']['pressure'] ?? 0,
        'timestamp' => time()
    ];
}

/**
 * Retorna respuesta de error en formato JSON
 */
function retornarError($mensaje, $codigo = 500)
{
    http_response_code($codigo);
    echo json_encode([
        'error' => true,
        'mensaje' => $mensaje,
        'timestamp' => time()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Retorna respuesta exitosa en formato JSON
 */
function retornarExito($datos)
{
    echo json_encode(array_merge($datos, [
        'error' => false,
        'mensaje' => 'Datos obtenidos correctamente'
    ]), JSON_UNESCAPED_UNICODE);
    exit;
}

// Ejecutar aplicacion principal
try {
    // Cargar variables de entorno
    if (!cargarVariablesEntorno()) {
        retornarError('No se pudo cargar el archivo de configuracion (.env)', 500);
    }

    // Obtener configuracion
    $apiKey = $_ENV['API_CLIMA'] ?? '';
    $ciudad = $_ENV['CIUDAD_DEFECTO'] ?? 'Santander';
    $pais = $_ENV['PAIS_DEFECTO'] ?? 'ES';
    $idioma = $_ENV['IDIOMA'] ?? 'es';

    // Validar API key
    if (empty($apiKey) || $apiKey === 'tu_clave_api_aqui') {
        retornarError('La clave de API no esta configurada. Por favor, actualiza el archivo .env', 400);
    }

    // Obtener datos del clima
    $datosApi = obtenerDatosClima($apiKey, $ciudad, $pais, $idioma);

    if (!$datosApi) {
        retornarError('Error al conectar con el servicio de clima', 503);
    }

    // Verificar si la respuesta de la API contiene errores
    if (isset($datosApi['cod']) && $datosApi['cod'] !== 200) {
        $mensajeError = $datosApi['message'] ?? 'Error desconocido de la API';
        retornarError("Error de la API del clima: $mensajeError", 400);
    }

    // Procesar datos
    $datosClima = procesarDatosClima($datosApi);

    if (!$datosClima) {
        retornarError('Error al procesar los datos del clima', 500);
    }

    // Retornar respuesta exitosa
    retornarExito($datosClima);
} catch (Exception $e) {
    error_log("Error en clima.php: " . $e->getMessage());
    retornarError('Error interno del servidor', 500);
}
