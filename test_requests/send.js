import http from 'http';
import crypto from 'crypto';

class PaymentApiTest {
    constructor() {
        // Базовая конфигурация
        this.config = {
            host: '127.0.0.1',
            port: 8000,
            path: '/api',
            merchantId: '173209465',
            secretKey: 'bb4c351b088e7f615a8230111424e76a'
        };
    }

    // Генерация подписи для запроса
    generateSignature(data) {
        const hashString = [
            data.shop,
            data.order,
            data.amount,
            data.currency,
            this.config.secretKey
        ].join(':');

        return crypto.createHash('sha256')
            .update(hashString)
            .digest('hex')
            .toUpperCase();
    }

    // Создание тестового платежного запроса
    createPaymentRequest(customData = {}) {
        const defaultData = {
            shop: this.config.merchantId,
            order: '1',
            amount: '1250.00',
            currency: 'RUB',
            username: 'testuser',
            handler: 'process'
        };

        const data = { ...defaultData, ...customData };
        data.signature = this.generateSignature(data);

        return data;
    }

    // Отправка HTTP запроса
    async sendRequest(data) {
        const formData = new URLSearchParams(data).toString();

        const options = {
            hostname: this.config.host,
            port: this.config.port,
            path: this.config.path,
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Content-Length': Buffer.byteLength(formData)
            }
        };

        return new Promise((resolve, reject) => {
            const req = http.request(options, (res) => {
                let responseData = '';

                res.on('data', (chunk) => {
                    responseData += chunk;
                });

                res.on('end', () => {
                    resolve({
                        statusCode: res.statusCode,
                        headers: res.headers,
                        redirectUrl: res.headers.location,
                        body: responseData
                    });
                });
            });

            req.on('error', (error) => {
                reject(error);
            });

            req.write(formData);
            req.end();
        });
    }

    // Парсинг параметров из URL редиректа
    parseRedirectUrl(url) {
        if (!url) return null;
        const urlParams = new URL(url);
        return Object.fromEntries(urlParams.searchParams);
    }

    // Следование по редиректу
    async followRedirect(redirectUrl) {
        const url = new URL(redirectUrl);
        
        const options = {
            hostname: url.hostname,
            port: url.port,
            path: `${url.pathname}${url.search}`,
            method: 'GET'
        };

        return new Promise((resolve, reject) => {
            const req = http.request(options, (res) => {
                let responseData = '';

                res.on('data', (chunk) => {
                    responseData += chunk;
                });

                res.on('end', () => {
                    resolve({
                        statusCode: res.statusCode,
                        headers: res.headers,
                        body: responseData
                    });
                });
            });

            req.on('error', (error) => {
                reject(error);
            });

            req.end();
        });
    }

    // Запуск тестов
    async runTests() {
        try {
            console.log('Запуск тестов платежного API...\n');

            // Тест 1: Корректный платежный запрос
            console.log('Тест 1: Корректный платежный запрос');
            const validResponse = await this.sendRequest(this.createPaymentRequest());
            if (validResponse.redirectUrl) {
                console.log('Начальный статус:', validResponse.statusCode);
                console.log('URL редиректа:', validResponse.redirectUrl);
                console.log('Параметры редиректа:', this.parseRedirectUrl(validResponse.redirectUrl));
                
                const redirectResponse = await this.followRedirect(validResponse.redirectUrl);
                console.log('Финальный статус:', redirectResponse.statusCode);
                console.log('Финальный ответ (первые 200 символов):', 
                    redirectResponse.body.substring(0, 200) + '...');
            }
            console.log('-------------------\n');

            // Тест 2: Неверная подпись
            console.log('Тест 2: Неверная подпись');
            const invalidData = this.createPaymentRequest();
            invalidData.signature = 'invalid_signature';
            const invalidResponse = await this.sendRequest(invalidData);
            console.log('Статус:', invalidResponse.statusCode);
            if (invalidResponse.redirectUrl) {
                console.log('URL редиректа:', invalidResponse.redirectUrl);
                const redirectResponse = await this.followRedirect(invalidResponse.redirectUrl);
                console.log('Финальный статус:', redirectResponse.statusCode);
            }
            console.log('-------------------\n');

            // Тест 3: Отсутствует обязательное поле
            console.log('Тест 3: Отсутствует обязательное поле (amount)');
            const incompleteData = this.createPaymentRequest();
            delete incompleteData.amount;
            const incompleteResponse = await this.sendRequest(incompleteData);
            console.log('Статус:', incompleteResponse.statusCode);
            if (incompleteResponse.redirectUrl) {
                console.log('URL редиректа:', incompleteResponse.redirectUrl);
            }
            console.log('-------------------\n');

            // Тест 4: Несуществующий магазин
            console.log('Тест 4: Несуществующий магазин');
            const invalidMerchantResponse = await this.sendRequest(
                this.createPaymentRequest({ shop: '999999999' })
            );
            console.log('Статус:', invalidMerchantResponse.statusCode);
            if (invalidMerchantResponse.redirectUrl) {
                console.log('URL редиректа:', invalidMerchantResponse.redirectUrl);
            }
            console.log('-------------------\n');

        } catch (error) {
            console.error('Ошибка выполнения теста:', error.message);
        }
    }

    // Вспомогательный метод для форматирования ответа
    formatResponse(response) {
        try {
            return JSON.parse(response);
        } catch {
            return response;
        }
    }
}

// Запуск тестов
const tester = new PaymentApiTest();
tester.runTests();