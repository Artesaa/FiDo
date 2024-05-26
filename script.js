// document.addEventListener('DOMContentLoaded', function () {
//     const form = document.getElementById('carForm');
//     const resultDiv = document.getElementById('result');

//     form.addEventListener('submit', function (event) {
//         event.preventDefault(); 
//         const formData = new FormData(form);

//         axios.post('/calculate', {
//             price: formData.get('price'),
//             date: formData.get('date'),
//             carType: formData.get('carType'),
//             engineSize: formData.get('engineSize'),
//             fuelType: formData.get('fuelType')
//         })
//         .then(function (response) {
//             const doganaValue = response.data.dogana_value; // Updated key
//             resultDiv.textContent = `Dogana Value: ${doganaValue}`;
//         })
//         .catch(function (error) {
//             console.error('Error:', error);
//         });
//     });
// });

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('importForm');
    form.addEventListener('submit', function (event) {
        event.preventDefault();

        const formData = new FormData(form);
        const jsonData = {};
        formData.forEach((value, key) => {
            jsonData[key] = value;
        });

        fetch('alg.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(jsonData),
        })
        .then(response => response.json())
        .then(data => {
            const resultDiv = document.getElementById('result');
            if (data.dogana_value !== undefined) {
                resultDiv.innerHTML = 'Dogana Value: ' + data.dogana_value;
            } else if (data.error !== undefined) {
                resultDiv.innerHTML = 'Error: ' + data.error;
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
});
