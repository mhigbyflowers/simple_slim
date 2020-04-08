<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

$app->get('/test', function (Request $request, Response $responce) {
    $sql = "select * from age_by_result";
    $params = $request->getQueryParams();
    if (isset($params['gender'])) {
        $sql = "select * from covid_demos where GENDER = :gender limit 10 ";

        try {
            $db = new db();
            $db = $db->connect();
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ":gender" => $gender
            ]);
            queryReturn($stmt);
        } catch (PDOException $e) {
            echo '{"error": {"text": ' . $e->getMessage() . '}';
        }
    } else {
        $sql = "select * from cohort limit 10";
        try {
            $db = new db();
            $db = $db->connect();
            $stmt = $db->query($sql);
            queryReturn($stmt);
        } catch (PDOException $e) {
            echo '{"error": {"text": ' . $e->getMessage() . '}';
        }

    }
});



function queryReturn($stmt)
{
    $payload = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $db = null;
    echo json_encode($payload);
}
