<?php
switch($_POST['action']) {
    case 'SEND':
        sendWichtel()
        break;
    default:
        return [
            "statusCode" => 404,
            "error" => "No action defined"
        ];
        break;
}