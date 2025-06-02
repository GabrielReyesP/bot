<?php

$token = '7553138734:AAEyLBFufqhstjus_kyeKMxv0zxXQ2-1r30';
$website = 'https://api.telegram.org/bot'.$token;
$input = file_get_contents('php://input');
$update = json_decode($input, TRUE);

if (!isset($update["message"])) {
    exit;
}

$message = $update["message"];
$chat_id = $message["chat"]["id"];
$text = $message["text"] ?? "";

// Función para enviar mensajes
function sendMessage($chat_id, $text) {
    global $API_URL;
    file_get_contents($API_URL . "sendMessage?chat_id=$chat_id&text=" . urlencode($text));
}

// Manejador de comandos
switch ($text) {
    case '/start':
        $msg = "🛒 ¡Bienvenido! Aquí tienes 4 productos recomendados:\n\n";
        $msg .= "1. 🍎 Manzanas - Pasillo: Frutas\n";
        $msg .= "2. 🥖 Pan integral - Pasillo: Panadería\n";
        $msg .= "3. 🧼 Jabón líquido - Pasillo: Aseo\n";
        $msg .= "4. 🥛 Leche descremada - Pasillo: Lácteos\n\n";
        $msg .= "¡Usa el menú para explorar más productos!";
        sendMessage($chat_id, $msg);
        break;

    default:
        sendMessage($chat_id, "❓ No entiendo ese comando. Escribe /start para ver recomendaciones.");
        break;
}
 if($text === '/start'){
        $response = "¡Hola! Bienvenido al asistente virtual del supermercado \n\npuedes";
    } else {
        $response = $productos[$text] ?? "Lo siento, no entiendo lo que quieres decir";
    };

if($message){
    $text = strtolower(trim($message['text'] ?? ''));
    $chatId = $message['chat']['id'] ?? '';
    $productos =[
        'carne'&&'queso'&&'jamon' => 'Pasillo 1',
        'leche'&&'Yogurth'&&'Cereal'=> 'pasillo 2',
        'Bebidas'&&'Jugos' => 'pasillo 3',
        'Pan'&&'Pasteles'&&'Tortas' => 'Pasillo 4',
        'Detergente'&&'Lavaloza' =>'Pasillo 5'
    ];
   
    $apiUrl = "https://api.telegram.org/bot{$botToken}/sendMessage";
    file_get_contents($apiUrl . "?chat_id={$chatId}&text=" . urldecode($response));

}   
