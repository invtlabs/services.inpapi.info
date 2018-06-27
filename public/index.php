<?php

// Load autoload.
require_once __DIR__ . "/../vendor/autoload.php";

// Load DotEnv
$dotenv = new Dotenv\Dotenv(__DIR__ . '/../');
$dotenv->load();

require_once __DIR__ . "/../src/dbPDO.php";
require_once __DIR__ . "/../src/functions.php";
require_once __DIR__ . "/../src/mockedFunctions.php";
require_once __DIR__ . "/../src/Crypt.php";


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


// Ho Gaming - Get Game Url.
router(array('POST', 'OPTIONS'), '^/api/hogaming/getGameUrl$', function() {

    // Set Content type to application/json
    header('Content-Type: application/json');

    if (isset($_POST['apiKey']) && isset($_POST['userKey']) && isset($_POST['lang'])) 
    {
        $request = new stdClass();
        $request->apiKey = $_POST['apiKey'];
        $request->userKey = $_POST['userKey'];
        $request->lang = (string) $_POST['lang'];

        $responseJSON = '';
        if (!validateHoGamingGetTokenParams($request, $responseJSON)) 
        {
            # code...
        }
        else
        {
            // Get User Info
            $userInfo = getUserInfoByUserKey($request->userKey);
            // Send Request
            $httpRequest = Requests::get(getenv('HO_URL') . '/cgibin/ClientLoginServlet?uname=' . $userInfo['userKey'] . '&currency=' . $userInfo['currencyCode'] . '&country=' . $userInfo['countryCode'] . '&fn=' . $userInfo['userName'] . '&ln=lastname&mode=1&commonwallet=true');
            // If http status code is OK
            if ($httpRequest->status_code == 200)
            {
                // Xml Load String
                $xml = simplexml_load_string($httpRequest->body);
                // Get Session ID
                $sessionId = (string) $xml->attribute[2]->value;    
                // Build Game URL
                $gameUrl = getenv('HO_URL') . '/login/visitor/cwlogin.jsp?sessionid=' . $sessionId . '&lang=' . $request->lang . '&version=2';


                $dataObj = array('status' => 'success', 'statusMsg' => 'OK', 'data' => ['gameUrl' => $gameUrl]);
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


// SA Gaming - Get Game Url.
router(array('POST', 'OPTIONS'), '^/api/sagaming/getToken$', function() {

    // Set Content type to application/json
    header('Content-Type: application/json');

    if (isset($_POST['apiKey']) && isset($_POST['userKey']) && isset($_POST['gameType']) && isset($_POST['lang']) && isset($_POST['mobile'])) 
    {
        $request = new stdClass();
        $request->apiKey = (string) $_POST['apiKey'];
        $request->userKey = (string) $_POST['userKey'];
        $request->gameType = (string) $_POST['gameType'];
        $request->lang = (string) $_POST['lang'];
        $request->mobile = (bool) $_POST['mobile'];

        $responseJSON = '';
        if (!validateSAGamingGetTokenParams($request, $responseJSON)) 
        {
            # code...
        }
        else
        {
            // Get User Info
            $userInfo = getUserInfoByUserKey($request->userKey);
            // Date
            $date = date('Ymdhis', time());
            // Query String
            $qs = 'method=LoginRequest&Key=' . getenv('SA_SECRET_KEY') . '&Time=' . $date . '&Username=' . $userInfo['userKey'] . '&CurrencyType=' . $userInfo['currencyCode'];
            $s = md5($qs . getenv('SA_MD5_KEY') . $date . getenv('SA_SECRET_KEY'));

            $crypt = new DES(getenv('SA_ENCRYPT_KEY'));
            $q = $crypt->encrypt($qs);

            $data = array('q' => $q, 's' => $s);

            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($data)
                )
            );

            $context  = stream_context_create($options);

            $result = file_get_contents(getenv('SA_API'), false, $context);

            if ($result === FALSE) { 
                $errorObj = array('status' => 'error', 'statusMsg' => 'Cannot retrieve Token from Provider.');
                $responseJSON = json_encode($errorObj);
            }

            $xml = simplexml_load_string($result);

            if ($xml->ErrorMsgId == 0) 
            {
                $dataObj = array('status' => 'success', 'data' => ['gameUrl' => getenv('SA_CLIENT_LOADER'), 'token' => (string) $xml->Token, 'username' => $request->userKey, 'lobby' => getenv('SA_LOBBY_CODE'), 'lang' => $request->lang, 'mobile' => $request->mobile]);
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
