from flask import Flask, render_template, request, jsonify
from flask_cors import CORS

app = Flask(__name__)
CORS(app)
def get_tariff_rate(first_registration, car_type, engine_size, fuel_type):
    tariff_rates = {
        "State": {
            "trades_car": {
                "engine_size": {"small": 0.1, "medium": 0.15, "large": 0.2},
                "fuel_type": {"gasoline": 0.05, "diesel": 0.1, "electric": 0.02, "hybrid": 0.03, "hydrogen": 0.02, "ethanol": 0.08}
            },
            "passenger_car": {
                "engine_size": {"small": 0.05, "medium": 0.1, "large": 0.15},
                "fuel_type": {"gasoline": 0.1, "diesel": 0.15, "electric": 0.03, "hybrid": 0.05, "hydrogen": 0.03, "ethanol": 0.1}
            }
        }
    } 
    return tariff_rates.get(first_registration, {}).get(car_type, {}).get("engine_size", {}).get(engine_size, 0.2) \
        + tariff_rates.get(first_registration, {}).get(car_type, {}).get("fuel_type", {}).get(fuel_type, 0.2)

def calculate_customs_duty(car_value, tariff_rate, vat_rate, excise_tax_rate, administrative_fee):
    customs_duty = car_value * tariff_rate
    vat = (car_value + customs_duty) * vat_rate
    excise_tax = (car_value + customs_duty) * excise_tax_rate
    total_duty = customs_duty + vat + excise_tax + administrative_fee
    return total_duty



@app.route('/submit', methods=['POST'])
def index():
    if request.method == 'POST':
        try:
            car_value = float(request.form['price'])
            first_registration = request.form['date']
            car_type = request.form['carType']
            engine_size = request.form['engineSize']
            fuel_type = request.form['fuelType']
        except KeyError:
            return jsonify({'error': 'Bad Request: Missing form field(s)'}), 400
        except ValueError:
            return jsonify({'error': 'Bad Request: Invalid value for price'}), 400

        tariff_rate = get_tariff_rate(first_registration, car_type, engine_size, fuel_type)
        vat_rate = 0.18
        excise_tax_rate = 0.05
        administrative_fee = 50

        customs_duty = calculate_customs_duty(car_value, tariff_rate, vat_rate, excise_tax_rate, administrative_fee)
        dogana_value = car_value - customs_duty

        return jsonify({'dogana_value': dogana_value})

    return render_template('index.html')

if __name__ == "__main__":
    import os
    os.environ['FLASK_ENV'] = 'production'
    app.run(debug=False)


