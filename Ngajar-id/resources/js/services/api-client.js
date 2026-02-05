/**
 * ApiClient - Wrapper untuk Fetch API
 * Otomatis menangani CSRF Token dan Error Handling standar.
 */

const ApiClient = {
    /**
     * Base request function
     */
    async request(url, options = {}) {
        // Ambil CSRF token dari meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            ...options.headers
        };

        const config = {
            ...options,
            headers
        };

        try {
            const response = await fetch(url, config);

            // Jika status 401 (Unauthorized), bisa redirect login
            if (response.status === 401) {
                window.location.href = '/login';
                return; // Stop execution
            }

            // Jika error HTTP (4xx, 5xx)
            if (!response.ok) {
                // Coba parsng JSON error
                const errorData = await response.json().catch(() => ({}));
                const error = new Error(errorData.message || 'Terjadi kesalahan sistem.');
                error.status = response.status;
                error.data = errorData;
                throw error;
            }

            // Parse JSON response
            return await response.json();

        } catch (error) {
            console.error('API Request Error:', error);
            throw error; // Lempar ulang biar bisa di-catch di komponen
        }
    },

    get(url, headers = {}) {
        return this.request(url, { method: 'GET', headers });
    },

    post(url, data, headers = {}) {
        return this.request(url, { 
            method: 'POST', 
            body: JSON.stringify(data), 
            headers 
        });
    },

    put(url, data, headers = {}) {
        return this.request(url, { 
            method: 'PUT', 
            body: JSON.stringify(data), 
            headers 
        });
    },

    delete(url, headers = {}) {
        return this.request(url, { method: 'DELETE', headers });
    }
};

export default ApiClient;
