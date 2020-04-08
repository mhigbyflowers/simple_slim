
<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;


$app->get('/api/{id}', function (Request $request, Response $response, $args) {
    if (in_array('gender', $args)) {
        genderSummary($request, $response);
    };
});
$app->get('/tbl/{id}', function (Request $request, Response $response, $args) {
    tableDump($request, $response, $args);
});

$slq_age = "select 
COUNT(CASE WHEN age_at_test BETWEEN 0 AND 9 THEN 1 END) as '0-9',
COUNT(CASE WHEN age_at_test BETWEEN 10 AND 10 THEN 2 END) as '10-19',
COUNT(CASE WHEN age_at_test BETWEEN 20 AND 29 THEN 2 END) as '20-29',
COUNT(CASE WHEN age_at_test BETWEEN 30 AND 39 THEN 2 END) as '30-39',
COUNT(CASE WHEN age_at_test BETWEEN 40 AND 49 THEN 2 END) as '40-49',
COUNT(CASE WHEN age_at_test BETWEEN 50 AND 59 THEN 2 END) as '50-59',
COUNT(CASE WHEN age_at_test BETWEEN 60 AND 69 THEN 2 END) as '60-69',
COUNT(CASE WHEN age_at_test BETWEEN 70 AND 79 THEN 2 END) as '70-79',
COUNT(CASE WHEN age_at_test BETWEEN 80 AND 89 THEN 2 END) as '80-89',
COUNT(CASE WHEN age_at_test BETWEEN 90 AND 99 THEN 2 END) as '90-99',
COUNT(CASE WHEN age_at_test > 100 THEN 2 END) as '100+' from cohort where COVID_test_result = :result";

$genderSql= 'select 
COUNT(CASE gender WHEN "M" THEN 1 END) as "male",
COUNT(CASE gender WHEN "F" THEN 2 END) as "female"
from cohort';

$raceSql= makeRaceSql();


function tableDump($request, $response, $args)
{
    $bindedParams = bindParams($request);
    $sql = 'select * from ' . $args['id'];
    if (isset($bindedParams)) {
        $sql .= ' where COVID_test_Result = :result';
        queryWithParamsReturn($sql, $bindedParams, $response);
    } else {
        queryReturn($sql, $response);
    }
}

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
