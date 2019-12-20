export interface IHttpResponse {
    status?: string;
    data?: any;
    message?: string;
    status_code?: number;
    total_data?: number;
    total_page?: number;
    current_page?: number;
    limit?: string;
}
