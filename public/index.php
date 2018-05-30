<?php

// Load autoload.
require_once __DIR__ . "/../vendor/autoload.php";

// Load DotEnv
$dotenv = new Dotenv\Dotenv(__DIR__ . '/../');
$dotenv->load();

require_once __DIR__ . "/../src/functions.php";
require_once __DIR__ . "/../src/mockedFunctions.php";


// CQ9 - Launch Game.
router(array('POST', 'OPTIONS'), '^/cq9/launchGame$', function() {

    // Set Content type to application/json
    header('Content-Type: application/json');

    if (isset($_POST['apiKey'])) 
    {
        $request = new stdClass();
        $request->apiKey = $_POST['apiKey'];
        $request->userName = $_POST['userName'];
        $request->gameCode = $_POST['gameCode'];

        $responseJSON = '';
        if (!validateCQ9LaunchgameParams($request, $responseJSON)) 
        {
            # code...
        }
        else if ($request->apiKey != getenv('API_KEY'))
        {
            $errorObj = array('status' => 'error', 'statusMsg' => 'Invalid Secret key.');
            $responseJSON = json_encode($errorObj);
        }
        else if (!getPlayerInfoBycustId($request->userName))
        {
            $errorObj = array('status' => 'error', 'statusMsg' => 'Username not found.');
            $responseJSON = json_encode($errorObj);
        }
        else if (!getGameInfo($request->gameCode))
        {
            $errorObj = array('status' => 'error', 'statusMsg' => 'Game not found.');
            $responseJSON = json_encode($errorObj);
        }
        else
        {
            // Get Game Info.
            $gameInfo = getGameInfo($request->gameCode);

            /**
             * Create Request.
             */
            
            $headers = array('Authorization' => getenv('CQ9_API_TOKEN'), 'Content-Type' => 'application/x-www-form-urlencoded');
            $data = array('account' => $request->userName, 'gamehall' => $gameInfo['hall'], 'gamecode' => $gameInfo['code'], 'gameplat' => $gameInfo['platform'], 'lang' => 'en');

            $httpRequest = Requests::post(getenv('CQ9_API_URL') . '/gameboy/player/sw/gamelink', $headers, $data);

            if ($httpRequest->status_code == 200) 
            {
                $data = json_decode($httpRequest->body, true);
                
                $dataObj = array('status' => 'success', 'statusMsg' => 'Game Launched', 'data' => ['gameUrl' => $data['data']['url']]);
                $responseJSON = json_encode($dataObj);
            }
            else
            {
                $errorObj = array('status' => 'error', 'statusMsg' => 'Game launch failed.');
                $responseJSON = json_encode($errorObj);
            }
        }
    }
    else
    {
        $errorObj = array('status' => 'error', 'statusMsg' => 'Missing or Invalid parameters.');
        $responseJSON = json_encode($errorObj);
    }

    echo $responseJSON;
});


// CQ9 - Launch Game.
router(array('POST', 'OPTIONS'), '^/cq9/logout$', function() {

    // Set Content type to application/json
    header('Content-Type: application/json');

    if (isset($_POST['apiKey']) && isset($_POST['userName'])) 
    {
        $request = new stdClass();
        $request->apiKey = $_POST['apiKey'];
        $request->userName = $_POST['userName'];

        $responseJSON = '';
        if (!validateCQ9LogoutParams($request, $responseJSON)) 
        {
            # code...
        }
        else if (!getPlayerInfoBycustId($request->userName))
        {
            $errorObj = array('status' => 'error', 'statusMsg' => 'Username not found.');
            $responseJSON = json_encode($errorObj);
        }
        else
        {
            $headers = array('Authorization' => getenv('CQ9_API_TOKEN'), 'Content-Type' => 'application/x-www-form-urlencoded');
            $data = array('account' => $request->userName);

            $httpRequest = Requests::post(getenv('CQ9_API_URL') . '/gameboy/player/logout', $headers, $data);

            if ($httpRequest->status_code == 200) 
            {
                $dataObj = array('status' => 'success', 'statusMsg' => 'User logout successfully.');
                $responseJSON = json_encode($dataObj);
            }
        }
    }
    else
    {
        $errorObj = array('status' => 'error', 'statusMsg' => 'Missing or Invalid parameters.');
        $responseJSON = json_encode($errorObj);
    }

    echo $responseJSON;
});
