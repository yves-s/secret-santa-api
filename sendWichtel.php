<?php
    // Allow from any origin
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }

    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

        exit(0);
    }

    include('inc/functions.php');
    include('inc/Mailman.php');
    session_start();

    $postdata = file_get_contents("php://input");
    $response = json_decode($postdata);
    $finalWichtel = array();

    processSecretSantas($response);

    function processSecretSantas($response) {
        $secretSantas = $response->secretSantas;
        $email = getEmails($secretSantas);
        $sender = $response->sender;
        $assignedSecretSantas = [];

        $i=0;
        while(!empty($secretSantas)) {

            $randomSecretSanta = rand(0, count($secretSantas)-1);
            $randomEmail = rand(0, count($secretSantas)-1);

            if (
                $secretSantas[$randomSecretSanta]->email == $email[$randomEmail] &&
                count($secretSantas) == 1
            ) {
                processSecretSantas($response);
            } else if ($secretSantas[$randomSecretSanta]->email != $email[$randomEmail]) {

                if(count($secretSantas) != 1) {

                    $assignedSecretSantas[$i] = [
                        'name' => $secretSantas[$randomSecretSanta]->name,
                        'email' => $email[$randomEmail]
                    ];
                    $secretSantas = unset_array($secretSantas, $randomSecretSanta);
                    $email = unset_array($email, $randomEmail);
                    $i++;

                } else if(count($secretSantas) == 1) {

                    $assignedSecretSantas[$i] = array(
                        'name' => $secretSantas[0]->name,
                        'email' => $email[$randomEmail]
                    );
                    unset_array($secretSantas, $randomSecretSanta);
                    unset_array($email, $randomEmail);

                    try {

                        $_SESSION['backupfile'] = sicherungskopie($assignedSecretSantas);
                        $mailman = new \Mailman\Mailman();
                        $res = $mailman->send($assignedSecretSantas, $sender);

                        die(json_encode($res));

                        die(
                            json_encode([
                                'statusCode' => '200',
                                'msg' => 'Deine Wichtel wurden verschickt',
                                'data' => $assignedSecretSantas
                            ])
                        );

                    } catch(Exception $e) {

                        die(
                            json_encode([
                                'statusCode' => '400',
                                'msg' => 'Es gab einen Error'
                            ])
                        );

                    }

                }

            }

        }
    }