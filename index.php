<?php

$token = '7553138734:AAEyLBFufqhstjus_kyeKMxv0zxXQ2-1r30';
$website = 'https://api.telegram.org/bot'.$token;
$input = file_get_contents('php://input');
$update = json_decode($input, TRUE);


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
    if($text === '/start'){
        $response = "¡Hola! Bienvenido al asistente virtual del supermercado \n\npuedes";
    } else {
        $response = $productos[$text] ?? "Lo siento, no entiendo lo que quieres decir";
    }
    $apiUrl = "https://api.telegram.org/bot{$botToken}/sendMessage";
    file_get_contents($apiUrl . "?chat_id={$chatId}&text=" . urldecode($response));

}   
