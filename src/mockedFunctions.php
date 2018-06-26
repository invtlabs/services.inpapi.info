<?php

include __DIR__ . "/../src/db.php";

function serverDateTime()
{
    date_default_timezone_set("Asia/Manila");
    return date('Y-m-d H:i:s');
}

function isNull($data)
{
    if (!empty($data)) return true;
}

function validUsername($userName)
{
    if (ctype_alnum($userName)) return true;
}

function validAmt($amount)
{
    if (is_numeric($amount)) return true;
}

function getPlayerInfoById($id)
{
    global $odb;
	$qry = $odb->prepare("SELECT * FROM `users` WHERE `id` = :id LIMIT 1;");
	$qry->execute(array(":id" => $id));
	if ($qry->rowCount() != 0) return $qry->fetch(PDO::FETCH_ASSOC);
}

function getUserInfoByUserKey($userKey)
{
    global $odb;
	$qry = $odb->prepare("SELECT * FROM `users` WHERE `userKey` = :userKey LIMIT 1;");
	$qry->execute(array(":userKey" => $userKey));
	if ($qry->rowCount() != 0) return $qry->fetch(PDO::FETCH_ASSOC);
}

function getGameInfo($gameCode)
{
    global $odb;
	$qry = $odb->prepare("SELECT * FROM `cq9_games` WHERE `code` = :code LIMIT 1;");
	$qry->execute(array(":code" => $gameCode));
	if ($qry->rowCount() != 0) return $qry->fetch(PDO::FETCH_ASSOC);
}

function validateCQ9LaunchgameParams($request, &$responseJSON)
{
    if (!isNull($request->apiKey)) 
    {
        $errorObj = array('status' => 'error', 'statusMsg' => 'Invalid Secret key.');
        $responseJSON = json_encode($errorObj);
        return false;
    }

    if (!isNull($request->userName)) 
    {
        $errorObj = array('status' => 'error', 'statusMsg' => 'Username not found.');
        $responseJSON = json_encode($errorObj);
        return false;
    }

    if (!isNull($request->gameCode)) 
    {
        $errorObj = array('status' => 'error', 'statusMsg' => 'Game code Not found.');
        $responseJSON = json_encode($errorObj);
        return false;
    }

    return true;
}


function validateCQ9LogoutParams($request, &$responseJSON)
{
    if (!isNull($request->apiKey)) 
    {
        $errorObj = array('status' => 'error', 'statusMsg' => 'Invalid Secret key.');
        $responseJSON = json_encode($errorObj);
        return false;
    }

    if (!isNull($request->userName)) 
    {
        $errorObj = array('status' => 'error', 'statusMsg' => 'Username not found.');
        $responseJSON = json_encode($errorObj);
        return false;
    }

    if (!validUsername($request->userName)) 
    {
        $errorObj = array('status' => 'error', 'statusMsg' => 'Username not found.');
        $responseJSON = json_encode($errorObj);
        return false;
    }

    return true;
}

function validateCMD368Transfer($request, &$responseJSON)
{
    if ($request->apiKey == '' || $request->userKey == '' || $request->type == '' || $request->amount == '') 
    {
        $errorObj = array('status' => 'error', 'statusMsg' => 'Parameters Error.');
        $responseJSON = json_encode($errorObj);
        return false;
    }

    if ($request->apiKey != getenv('API_KEY'))
    {
        $errorObj = array('status' => 'error', 'statusMsg' => 'Invalid Secret key.');
        $responseJSON = json_encode($errorObj);
        return false;
    }

    if (!validUsername($request->userKey))
    {
        $errorObj = array('status' => 'error', 'statusMsg' => 'Username not found.');
        $responseJSON = json_encode($errorObj);
        return false;
    }

    $typeActions = array('Deposit', 'Withdrawal');
    if (!in_array($request->type, $typeActions)) 
    {
        $errorObj = array('status' => 'error', 'statusMsg' => 'Invalid type of Transfer.');
        $responseJSON = json_encode($errorObj);
        return false;
    }

    if (!validAmt($request->amount)) 
    {
        $errorObj = array('status' => 'error', 'statusMsg' => 'Invalid Amount.');
        $responseJSON = json_encode($errorObj);
        return false;
    }

    return true;
}

function validateHoGamingGetTokenParams($request, &$responseJSON)
{
    if ($request->apiKey == '' || $request->userKey == '') 
    {
        $errorObj = array('status' => 'error', 'statusMsg' => 'Parameters Error.');
        $responseJSON = json_encode($errorObj);
        return false;
    }

    if ($request->apiKey != getenv('API_KEY'))
    {
        $errorObj = array('status' => 'error', 'statusMsg' => 'Invalid Secret key.');
        $responseJSON = json_encode($errorObj);
        return false;
    }

    if (!validUsername($request->userKey))
    {
        $errorObj = array('status' => 'error', 'statusMsg' => 'Username not found.');
        $responseJSON = json_encode($errorObj);
        return false;
    }

    return true;
}