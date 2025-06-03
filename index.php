<?php

$token = '7553138734:AAEyLBFufqhstjus_kyeKMxv0zxXQ2-1r30';

$website = 'https://api.telegram.org/bot'.$token;
$input = file_get_contents('php://input');
$update = json_decode($input, true);

$productos = [
    // Pasillo 1
    'carne' => 'Pasillo 1',
    'queso' => 'Pasillo 1',
    'jamón' => 'Pasillo 1',

    // Pasillo 2
    'leche' => 'Pasillo 2',
    'yogurth' => 'Pasillo 2',
    'cereal' => 'Pasillo 2',

    // Pasillo 3
    'bebidas' => 'Pasillo 3',
    'jugos' => 'Pasillo 3',

    // Pasillo 4
    'pan' => 'Pasillo 4',
    'pasteles' => 'Pasillo 4',
    'tortas' => 'Pasillo 4',

    // Pasillo 5
    'detergente' => 'Pasillo 5',
    'lavaloza' => 'Pasillo 5',
];

// Procesar mensaje
if (isset($update["message"])) {
    $message = $update["message"];
    $chat_id = $message["chat"]["id"];
    $text = strtolower(trim($message["text"] ?? ""));

    if ($text === "/start") {
        $msg = "👋 ¡Hola! Envíame el nombre de un producto y te diré en qué pasillo se encuentra.";
        sendMessage($chat_id, $msg);
    } elseif (isset($productos[$text])) {
        $pasillo = $productos[$text];
        sendMessage($chat_id, "El producto *$text* se encuentra en *$pasillo*.", true);
    } else {
        sendMessage($chat_id, "No entiendo la pregunta.");
    }
}

// Función para enviar mensajes
function sendMessage($chat_id, $text, $markdown = false) {
    global $API_URL;
    $data = [
        'chat_id' => $chat_id,
        'text' => $text,
    ];
    if ($markdown) {
        $data['parse_mode'] = 'Markdown';
    }
    file_get_contents($API_URL . "sendMessage?" . http_build_query($data));
    

}
?>
