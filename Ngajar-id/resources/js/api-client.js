/**
 * API Client - Centralized API consumption for frontend
 * Handles all HTTP requests to Laravel API endpoints
 */

class ApiClient {
    constructor() {
        // Get API base URL from data attribute or environment
        this.baseUrl = document.documentElement.getAttribute('data-api-url') ||
                       window.API_URL ||
                       `${window.location.origin}/api/v1`;

        // Get CSRF token from meta tag
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        // Get auth token from localStorage
        this.authToken = localStorage.getItem('auth_token') || '';
    }

    /**
     * Get authorization headers
     */
    getHeaders(isFormData = false) {
        const headers = {
            'X-CSRF-TOKEN': this.csrfToken,
            'Accept': 'application/json',
        };

        if (!isFormData) {
            headers['Content-Type'] = 'application/json';
        }

        if (this.authToken) {
            headers['Authorization'] = `Bearer ${this.authToken}`;
        }

        return headers;
    }

    /**
     * Generic fetch wrapper
     */
    async request(endpoint, options = {}) {
        const {
            method = 'GET',
            body = null,
            isFormData = false,
            throwError = true,
        } = options;

        const url = `${this.baseUrl}${endpoint}`;
        const config = {
            method,
            headers: this.getHeaders(isFormData),
        };

        if (body) {
            config.body = isFormData ? body : JSON.stringify(body);
        }

        try {
            const response = await fetch(url, config);
            const data = await response.json();

            if (!response.ok && throwError) {
                throw new Error(data.message || `HTTP ${response.status}: ${response.statusText}`);
            }

            return {
                success: response.ok,
                data,
                status: response.status,
            };
        } catch (error) {
            console.error(`API Error [${method} ${endpoint}]:`, error);
            throw error;
        }
    }

    // ========== AUTHENTICATION ==========

    async login(email, password) {
        const result = await this.request('/login', {
            method: 'POST',
            body: { email, password },
        });

        if (result.success && result.data.data?.token) {
            this.authToken = result.data.data.token;
            localStorage.setItem('auth_token', this.authToken);
        }

        return result.data;
    }

    async logout() {
        const result = await this.request('/user/logout', { method: 'POST' });
        localStorage.removeItem('auth_token');
        this.authToken = '';
        return result.data;
    }

    async getCurrentUser() {
        const result = await this.request('/user');
        return result.data;
    }

    // ========== COURSES - SEARCH & BROWSE ==========

    async searchCourses(query = '', filters = {}) {
        const params = new URLSearchParams({
            q: query,
            page: filters.page || 1,
            per_page: filters.per_page || 12,
            sort_by: filters.sort_by || 'newest',
            ...filters,
        });

        const result = await this.request(`/search/courses?${params}`);
        return result.data;
    }

    async getBrowseFilters() {
        const result = await this.request('/search/filters');
        return result.data;
    }

    async getTrendingCourses(limit = 10) {
        const result = await this.request(`/search/trending?limit=${limit}`);
        return result.data;
    }

    async getCategories() {
        const result = await this.request('/search/categories');
        return result.data;
    }

    async getCourseDetail(courseId) {
        const result = await this.request(`/kelas/${courseId}`);
        return result.data;
    }

    async getCourseReviews(courseId, page = 1) {
        const result = await this.request(`/courses/${courseId}/reviews?page=${page}`);
        return result.data;
    }

    // ========== ENROLLMENT ==========

    async checkEnrollmentEligibility(courseId) {
        const result = await this.request('/enrollment/check', {
            method: 'POST',
            body: { kelas_id: courseId },
        });
        return result.data;
    }

    async getEnrollmentRequirements(courseId) {
        const result = await this.request(`/enrollment/requirements/${courseId}`);
        return result.data;
    }

    async enrollCourse(courseId) {
        const result = await this.request(`/kelas/${courseId}/enroll`, {
            method: 'POST',
        });
        return result.data;
    }

    // ========== STUDENT PROGRESS ==========

    async getMyProgress() {
        const result = await this.request('/my-progress');
        return result.data;
    }

    async getCourseProgress(courseId) {
        const result = await this.request(`/my-progress/${courseId}`);
        return result.data;
    }

    async completeMaterial(materialId) {
        const result = await this.request(`/my-progress/materi/${materialId}/complete`, {
            method: 'POST',
        });
        return result.data;
    }

    async getMyCourses(page = 1) {
        const result = await this.request(`/my-courses?page=${page}`);
        return result.data;
    }

    // ========== CERTIFICATES ==========

    async getMyCertificates(page = 1) {
        const result = await this.request(`/certificates?page=${page}`);
        return result.data;
    }

    async generateCertificate(courseId) {
        const result = await this.request(`/certificates/generate/${courseId}`, {
            method: 'POST',
        });
        return result.data;
    }

    async verifyCertificate(certificateNumber) {
        const result = await this.request(`/certificates/verify/${certificateNumber}`, {
            throwError: false,
        });
        return result.data;
    }

    // ========== REVIEWS & RATINGS ==========

    async addCourseReview(courseId, rating, comment) {
        const result = await this.request(`/courses/${courseId}/reviews`, {
            method: 'POST',
            body: { rating, comment },
        });
        return result.data;
    }

    async addMaterialFeedback(materialId, rating, feedback) {
        const result = await this.request(`/materials/${materialId}/feedback`, {
            method: 'POST',
            body: { rating, feedback },
        });
        return result.data;
    }

    // ========== LEADERBOARD ==========

    async getGlobalLeaderboard(page = 1) {
        const result = await this.request(`/leaderboard/global?page=${page}`);
        return result.data;
    }

    async getMyRank() {
        const result = await this.request('/leaderboard/my-rank');
        return result.data;
    }

    async getMyAchievements() {
        const result = await this.request('/achievements/my');
        return result.data;
    }

    // ========== NOTIFICATIONS ==========

    async getNotifications(page = 1) {
        const result = await this.request(`/notifications?page=${page}`);
        return result.data;
    }

    async getUnreadCount() {
        const result = await this.request('/notifications/unread-count');
        return result.data;
    }

    async markNotificationAsRead(notificationId) {
        const result = await this.request(`/notifications/${notificationId}/read`, {
            method: 'PUT',
        });
        return result.data;
    }

    // ========== LEARNING PATHS ==========

    async getLearningPaths(page = 1) {
        const result = await this.request(`/learning-paths-api?page=${page}`);
        return result.data;
    }

    async getLearningPathDetail(pathId) {
        const result = await this.request(`/learning-paths-api/${pathId}`);
        return result.data;
    }

    async enrollLearningPath(pathId) {
        const result = await this.request(`/learning-paths-api/${pathId}/enroll`, {
            method: 'POST',
        });
        return result.data;
    }

    async getLearningPathProgress(pathId) {
        const result = await this.request(`/learning-paths-api/${pathId}/progress`);
        return result.data;
    }
}

// Export for use in other scripts
window.ApiClient = ApiClient;
const api = new ApiClient();
window.api = api;
