import http from 'k6/http';
import { sleep } from 'k6';

export const options = {
    stages: [
        { duration: '30s', target: 50 },   // Carga normal
        { duration: '1m', target: 200 },   // Carga alta
        { duration: '30s', target: 500 },  // Pico máximo
        { duration: '30s', target: 0 },    // Bajada
    ],
};

export default function () {
    http.get('http://proyect.test');        // Listado coches
    sleep(1);
    http.get('http://proyect.test');      // Detalle coche
    sleep(1);
}
