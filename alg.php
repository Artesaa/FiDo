
<?php

function getTariffRate($first_registration, $car_type, $engine_size, $fuel_type) {
    $tariff_rates = array(
        "State" => array(
            "trades_car" => array(
                "engine_size" => array("small" => 0.1, "medium" => 0.15, "large" => 0.2),
                "fuel_type" => array("gasoline" => 0.05, "diesel" => 0.1, "electric" => 0.02, "hybrid" => 0.03, "hydrogen" => 0.02, "ethanol" => 0.08)
            ),
            "passenger_car" => array(
                "engine_size" => array("small" => 0.05, "medium" => 0.1, "large" => 0.15),
                "fuel_type" => array("gasoline" => 0.1, "diesel" => 0.15, "electric" => 0.03, "hybrid" => 0.05, "hydrogen" => 0.03, "ethanol" => 0.1)
            )
        )
    );

    return $tariff_rates[$first_registration][$car_type]["engine_size"][$engine_size] ?? 0.2
        + $tariff_rates[$first_registration][$car_type]["fuel_type"][$fuel_type] ?? 0.2;
}

function calculateCustomsDuty($car_value, $tariff_rate, $vat_rate, $excise_tax_rate, $administrative_fee) {
    $customs_duty = $car_value * $tariff_rate;
    $vat = ($car_value + $customs_duty) * $vat_rate;
    $excise_tax = ($car_value + $customs_duty) * $excise_tax_rate;
    $total_duty = $customs_duty + $vat + $excise_tax + $administrative_fee;
    return $total_duty;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    try {
        $car_value = floatval($input['price']);
        $first_registration = $input['date'];
        $car_type = $input['carType'];
        $engine_size = $input['engineSize'];
        $fuel_type = $input['fuelType'];
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(array('error' => 'Bad Request: Invalid input data'));
        exit();
    }

    $tariff_rate = getTariffRate($first_registration, $car_type, $engine_size, $fuel_type);
    $vat_rate = 0.18;
    $excise_tax_rate = 0.05;
    $administrative_fee = 50;

    $customs_duty = calculateCustomsDuty($car_value, $tariff_rate, $vat_rate, $excise_tax_rate, $administrative_fee);
    $dogana_value = $car_value - $customs_duty;

    echo json_encode(array('dogana_value' => $dogana_value));
} else {
    http_response_code(405);
    echo json_encode(array('error' => 'Method Not Allowed'));
}
?>
