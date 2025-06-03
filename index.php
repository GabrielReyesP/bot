<?php
// telegram_bot.php - Bot de Telegram para localizar productos en supermercado

// Configuración del bot
define('BOT_TOKEN', '7553138734:AAEyLBFufqhstjus_kyeKMxv0zxXQ2-1r30'); // Reemplazar con tu token real
define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');

// Base de datos de productos y pasillos
$productos_pasillos = [
    // Pasillo 1
    'carne' => 1,
    'queso' => 1,
    'jamon' => 1,
    'jamón' => 1,
    
    // Pasillo 2
    'leche' => 2,
    'yogurt' => 2,
    'yogurth' => 2,
    'cereal' => 2,
    
    // Pasillo 3
    'bebidas' => 3,
    'bebida' => 3,
    'jugos' => 3,
    'jugo' => 3,
    
    // Pasillo 4
    'pan' => 4,
    'pasteles' => 4,
    'pastel' => 4,
    'tortas' => 4,
    'torta' => 4,
    
    // Pasillo 5
    'detergente' => 5,
    'lavaloza' => 5,
    'lavalozas' => 5
];

// Descripciones de cada pasillo
$descripcion_pasillos = [
    1 => "🥩 **Pasillo 1 - Carnes y Embutidos**\n• Carne\n• Queso\n• Jamón",
    2 => "🥛 **Pasillo 2 - Lácteos y Cereales**\n• Leche\n• Yogurt\n• Cereal",
    3 => "🥤 **Pasillo 3 - Bebidas**\n• Bebidas\n• Jugos",
    4 => "🍞 **Pasillo 4 - Panadería**\n• Pan\n• Pasteles\n• Tortas",
    5 => "🧽 **Pasillo 5 - Limpieza**\n• Detergente\n• Lavaloza"
];

/**
 * Función para enviar mensajes al usuario
 */
function sendMessage($chat_id, $message, $parse_mode = 'Markdown') {
    $url = API_URL . 'sendMessage';
    $data = [
        'chat_id' => $chat_id,
        'text' => $message,
        'parse_mode' => $parse_mode
    ];
    
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    return file_get_contents($url, false, $context);
}

/**
 * Función para buscar producto en los pasillos
 */
function buscarProducto($texto, $productos_pasillos) {
    $texto_limpio = strtolower(trim($texto));
    
    // Buscar coincidencia exacta
    if (isset($productos_pasillos[$texto_limpio])) {
        return $productos_pasillos[$texto_limpio];
    }
    
    // Buscar coincidencia parcial
    foreach ($productos_pasillos as $producto => $pasillo) {
        if (strpos($texto_limpio, $producto) !== false || strpos($producto, $texto_limpio) !== false) {
            return $pasillo;
        }
    }
    
    return null;
}

/**
 * Función para generar respuesta de ayuda
 */
function generarAyuda() {
    global $descripcion_pasillos;
    
    $ayuda = "🛒 **¡Bienvenido al Supermercado!**\n\n";
    $ayuda .= "Te ayudo a encontrar productos. Escribe el nombre del producto que buscas.\n\n";
    $ayuda .= "**Nuestros pasillos:**\n\n";
    
    foreach ($descripcion_pasillos as $pasillo => $descripcion) {
        $ayuda .= $descripcion . "\n\n";
    }
    
    $ayuda .= "💡 **Ejemplos de uso:**\n";
    $ayuda .= "• Escribe: \"carne\" → Te diré que está en el Pasillo 1\n";
    $ayuda .= "• Escribe: \"leche\" → Te diré que está en el Pasillo 2\n";
    $ayuda .= "• Escribe: \"/ayuda\" → Para ver este mensaje nuevamente";
    
    return $ayuda;
}

/**
 * Función principal para procesar mensajes
 */
function procesarMensaje($update) {
    global $productos_pasillos, $descripcion_pasillos;
    
    if (!isset($update['message'])) return;
    
    $message = $update['message'];
    $chat_id = $message['chat']['id'];
    $text = isset($message['text']) ? $message['text'] : '';
    $user_name = isset($message['from']['first_name']) ? $message['from']['first_name'] : 'Usuario';
    
    // Comandos especiales
    if ($text === '/start' || $text === '/ayuda' || strtolower($text) === 'ayuda') {
        $respuesta = generarAyuda();
        sendMessage($chat_id, $respuesta);
        return;
    }
    
    if ($text === '/pasillos' || strtolower($text) === 'pasillos') {
        $respuesta = "🏪 **Lista de todos los pasillos:**\n\n";
        foreach ($descripcion_pasillos as $pasillo => $descripcion) {
            $respuesta .= $descripcion . "\n\n";
        }
        sendMessage($chat_id, $respuesta);
        return;
    }
    
    // Buscar producto
    if (!empty($text)) {
        $pasillo_encontrado = buscarProducto($text, $productos_pasillos);
        
        if ($pasillo_encontrado) {
            $respuesta = "✅ ¡Hola $user_name!\n\n";
            $respuesta .= "🔍 **Producto buscado:** $text\n";
            $respuesta .= "📍 **Ubicación:** Pasillo $pasillo_encontrado\n\n";
            $respuesta .= $descripcion_pasillos[$pasillo_encontrado] . "\n\n";
            $respuesta .= "¿Necesitas buscar otro producto? 😊";
        } else {
            $respuesta = "❌ Lo siento $user_name, no encontré \"$text\" en nuestros pasillos.\n\n";
            $respuesta .= "🔍 **Productos disponibles:**\n";
            
            $productos_unicos = array_unique(array_keys($productos_pasillos));
            $lista_productos = implode(', ', array_map('ucfirst', $productos_unicos));
            $respuesta .= $lista_productos . "\n\n";
            $respuesta .= "💡 Escribe \"/ayuda\" para ver todos los pasillos o intenta con otro producto.";
        }
        
        sendMessage($chat_id, $respuesta);
    }
}

// Webhook handler - Procesar actualizaciones de Telegram
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $update = json_decode($input, true);
    
    if ($update) {
        procesarMensaje($update);
    }
    
    http_response_code(200);
    exit;
}

// Interfaz web para configuración y pruebas
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bot Supermercado - Panel de Control</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { text-align: center; margin-bottom: 30px; }
        .status { padding: 15px; border-radius: 5px; margin: 15px 0; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .pasillo { background: #f8f9fa; padding: 15px; margin: 10px 0; border-left: 4px solid #007bff; }
        .btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        pre { background: #f1f1f1; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛒 Bot de Supermercado</h1>
            <p>Sistema de localización de productos</p>
        </div>

        <?php
        // Verificar configuración
        $token_configurado = (BOT_TOKEN !== '7553138734:AAEyLBFufqhstjus_kyeKMxv0zxXQ2-1r30');
        
        if (!$token_configurado): ?>
            <div class="status error">
                <strong>⚠️ Configuración requerida:</strong><br>
                Debes configurar tu BOT_TOKEN en la línea 6 del archivo PHP.
            </div>
        <?php else: ?>
            <div class="status success">
                <strong>✅ Bot configurado correctamente</strong>
            </div>
        <?php endif; ?>

        <h2>📋 Configuración de Pasillos</h2>
        <?php foreach ($descripcion_pasillos as $numero => $descripcion): ?>
            <div class="pasillo">
                <?php echo nl2br($descripcion); ?>
            </div>
        <?php endforeach; ?>

        <h2>🔧 Instrucciones de Instalación</h2>
        <div class="status warning">
            <h3>Paso 1: Crear el Bot en Telegram</h3>
            <ol>
                <li>Busca <strong>@BotFather</strong> en Telegram</li>
                <li>Envía <code>/newbot</code></li>
                <li>Sigue las instrucciones para crear tu bot</li>
                <li>Copia el token que te proporcione</li>
            </ol>

            <h3>Paso 2: Configurar el Webhook</h3>
            <p>Usa esta URL para configurar el webhook:</p>
            <pre>https://api.telegram.org/bot[TU_TOKEN]/setWebhook?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?></pre>
            
            <h3>Paso 3: Comandos del Bot</h3>
            <ul>
                <li><strong>/start</strong> - Mensaje de bienvenida</li>
                <li><strong>/ayuda</strong> - Mostrar ayuda completa</li>
                <li><strong>/pasillos</strong> - Listar todos los pasillos</li>
                <li><strong>[nombre producto]</strong> - Buscar ubicación del producto</li>
            </ul>
        </div>

        <h2>🧪 Probar el Bot</h2>
        <p>Una vez configurado, puedes probar enviando estos mensajes:</p>
        <ul>
            <li>"carne" → Responderá Pasillo 1</li>
            <li>"leche" → Responderá Pasillo 2</li>
            <li>"detergente" → Responderá Pasillo 5</li>
        </ul>
    </div>
</body>
</html>
