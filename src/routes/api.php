
<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;


$app->get('/api/{id}', function (Request $request, Response $response, $args) {
//do things with the {id}

});



function bindParams($request)
{
    $params = $request->getQueryParams();
    if (isset($params) && $params['result'] != 'ALL') {
        $paramBinds = [];
        foreach ($params as $key => $value) {
            $paramBinds[$key] = $value;
        }
        return $paramBinds;
    }
}

function queryWithParamsReturn($sql, $paramBinds, $response)
{
    try {
        $db = new db();
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute($paramBinds);
    } catch (PDOException $e) {
        echo '{"error": {"text": ' . $e->getMessage() . '}';
    }
    $payload = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $db = null;
    $response->withJson($payload);
}

function queryReturn($sql, $response, $paramBinds = null)
{
    try {
        $db = new db();
        $db = $db->connect();
        $stmt = $db->prepare($sql);
        $stmt->execute();
    } catch (PDOException $e) {
        echo '{"error": {"text": ' . $e->getMessage() . '}';
    }
    $payload = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $db = null;
    $response->withJson($payload);
}
