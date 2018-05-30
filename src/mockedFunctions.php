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

function getPlayerInfoById($id)
{
    global $odb;
	$qry = $odb->prepare("SELECT * FROM `users` WHERE `id` = :id LIMIT 1;");
	$qry->execute(array(":id" => $id));
	if ($qry->rowCount() != 0) return $qry->fetch(PDO::FETCH_ASSOC);
}

function getPlayerInfoBycustId($custId)
{
    global $odb;
	$qry = $odb->prepare("SELECT * FROM `users` WHERE `custId` = :custId LIMIT 1;");
	$qry->execute(array(":custId" => $custId));
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