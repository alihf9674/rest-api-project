<?php

include_once "../../../loader.php";

use App\services\CityService;
use App\utilities\Response;
use App\utilities\CacheUtility;

# check authorization (use a jwt token)
$token = getBearerToken();
$user = isValidToken($token);
if (!$user) {
    Response::respondAndDie(['Invalid Token'], Response::HTTP_UNAUTHORIZED);
}
$request_method = $_SERVER['REQUEST_METHOD'];
$request_body = json_decode(file_get_contents('php://input'), true);
$city_service = new CityService();


switch ($request_method) {
    case 'GET':
        $province_id = $_GET['province_id'] ?? null;
        if (!hasAccesstoProvinces($user, $province_id)) {
            Response::respondAndDie(['You have no access to this province'], Response::HTTP_FORBIDDEN);
        }
        CacheUtility::start();

        $request_data = [
            'province_id' => $province_id,
            'fields' => $_GET['fields'] ?? null,
            'orderby' => $_GET['orderby'] ?? null,
            'page' => $_GET['page'] ?? null,
            'page_size' => $_GET['page_size'] ?? null
        ];

        $response = $city_service->getCities($request_data);    //adaptor design pattern
        if (empty($response)) {
            Response::respondAndDie($response, Response::HTTP_NOT_FOUND);
        }
        echo Response::respond($response, Response::HTTP_OK);
        CacheUtility::end();
        die();

    case 'POST':
        if (!isValidCity($request_body)) {
            Response::respondAndDie("[Error: Invalid city data...]", Response::HTTP_NOT_ACCEPTABLE);
        }
        $response = $city_service->createCity($request_body);

        Response::respondAndDie($response, Response::HTTP_CREATED);

    case 'PUT':
        [$city_id, $city_name] = [$request_body['city_id'], $request_body['name']];
        if (!is_numeric($city_id) or empty($city_name)) {
            Response::respondAndDie("[Error: Invalid city data...]", Response::HTTP_NOT_ACCEPTABLE);
        }

        $result = $city_service->updateCityName($city_id, $city_name);
        if (!empty($result)) {
            Response::respondAndDie($result, Response::HTTP_OK);
        }
        Response::respondAndDie(["Error: not found any city!"], Response::HTTP_NOT_FOUND);

    case 'DELETE':
        $city_id = $_GET['city_id'] ?? null;

        if (!is_numeric($city_id) or is_null($city_id)) {
            Response::respondAndDie("[Error: Invalid city id...]", Response::HTTP_NOT_ACCEPTABLE);
        }
        $result = $city_service->deleteCityById($city_id);
        Response::respondAndDie($result, Response::HTTP_OK);

    default:
        Response::respondAndDie(["Invalid request method"], Response::HTTP_METHOD_NOT_ALLOWED);
}
