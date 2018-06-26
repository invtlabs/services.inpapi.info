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


// CMD368 - Transfer
router(array('POST', 'OPTIONS'), '^/api/cmd368/transfer$', function() {

    // Set Content type to application/json
    header('Content-Type: application/json');

    if (isset($_POST['apiKey']) && isset($_POST['userKey']) && isset($_POST['type']) && isset($_POST['amount'])) 
    {
        $request = new stdClass();
        $request->apiKey = $_POST['apiKey'];
        $request->userKey = $_POST['userKey'];
        $request->type = $_POST['type'];
        $request->amount = $_POST['amount'];

        $responseJSON = '';
        if (!validateCMD368Transfer($request, $responseJSON)) 
        {
            # code...
        }
        else
        {
            // Get User Info.
            $userInfo = getUserInfoByUserKey($request->userKey);
            if (!$userInfo) 
            {
                $errorObj = array('status' => 'error', 'statusMsg' => 'Username not found.');
                $responseJSON = json_encode($errorObj);
            }
            else
            {
                if ($request->type == 'Deposit') 
                {
                    $request = Requests::post(getenv('CMD368_API') . '/SportsApi.aspx?Method=fundtransfer&PartnerKey=');
                }
                else if ($request->type == 'Withdrawal')
                {

                }
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


// Ho Gaming - Get Token.
router(array('POST', 'OPTIONS'), '^/api/hogaming/getToken$', function() {

    // Set Content type to application/json
    header('Content-Type: application/json');

    if (isset($_POST['apiKey']) && isset($_POST['userKey'])) 
    {
        $request = new stdClass();
        $request->apiKey = $_POST['apiKey'];
        $request->userKey = $_POST['userKey'];

        $responseJSON = '';
        if (!validateHoGamingGetTokenParams($request, $responseJSON)) 
        {
            # code...
        }
        else
        {
            $userInfo = getUserInfoByUserKey($request->userKey);
            $request = Requests::get(getenv('HO_URL') . '/cgibin/ClientLoginServlet?uname=' . $userInfo['userKey'] . '&currency=' . $userInfo['currencyCode'] . '&country=' . $userInfo['countryCode'] . '&fn=' . $userInfo['userName'] . '&ln=lastname&mode=1&commonwallet=true');
            if ($request->status_code == 200)
            {
                // Xml Load String
                $xml = simplexml_load_string($request->body);
                // Get Session ID
                $sessionId = (string) $xml->attribute[2]->value;


                $dataObj = array('status' => 'success', 'statusMsg' => 'OK', 'sessionId' => $sessionId);
                $responseJSON = json_encode($dataObj);
            }
            else
            {
                $errorObj = array('status' => 'error', 'statusMsg' => 'Cannot retrieve Token from Provider.');
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
