<!DOCTYPE html>
<html>

<head>
    <title>Test Payment Form</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-800">Test Payment</h2>
                <p class="text-gray-600">Amount: {{ number_format($amount, 2) }} {{ $currency }}</p>
            </div>

            <form id="paymentForm" class="space-y-6">
                <input type="hidden" name="external_id" value="{{ $payment->external_id }}">

                <div>
                    <label class="block text-sm font-medium text-gray-700">Card Number</label>
                    <input type="text" name="card_number"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="4242 4242 4242 4242">
                    <div class="mt-1 text-sm text-gray-500">
                        Test Cards:
                        <ul class="list-disc list-inside">
                            <li>4242 4242 4242 4242 - Success</li>
                            <li>4000 0000 0000 0002 - Declined</li>
                            <li>4000 0000 0000 9995 - Insufficient Funds</li>
                        </ul>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Expiry</label>
                        <input type="text" name="expiry"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="MM/YY">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">CVV</label>
                        <input type="text" name="cvv"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="123">
                    </div>
                </div>

                <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Pay {{ number_format($amount, 2) }} {{ $currency }}
                </button>
            </form>

            <div id="result" class="mt-4 text-center hidden">
                <div id="success" class="text-green-600 hidden">
                    Payment successful! Redirecting...
                </div>
                <div id="error" class="text-red-600 hidden"></div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const resultDiv = document.getElementById('result');
            const successDiv = document.getElementById('success');
            const errorDiv = document.getElementById('error');

            // Собираем данные формы
            const formData = new FormData(form);

            // Отправляем запрос
            fetch('{{ route("test.payment.process") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(Object.fromEntries(formData))
                })
                .then(response => response.json())
                .then(data => {
                    resultDiv.classList.remove('hidden');

                    if (data.status === 'success') {
                        successDiv.classList.remove('hidden');
                        errorDiv.classList.add('hidden');

                        // Редирект через 2 секунды
                        setTimeout(() => {
                            window.location.href = '{{ $return_url }}';
                        }, 2000);
                    } else {
                        errorDiv.textContent = data.message;
                        errorDiv.classList.remove('hidden');
                        successDiv.classList.add('hidden');
                    }
                })
                .catch(error => {
                    resultDiv.classList.remove('hidden');
                    errorDiv.textContent = 'Payment processing failed';
                    errorDiv.classList.remove('hidden');
                    successDiv.classList.add('hidden');
                });
        });
    </script>
</body>

</html>