import axios, { AxiosError, AxiosRequestConfig } from "axios";

const API_URL = import.meta.env.VITE_API_URL;

interface FetchRequest extends AxiosRequestConfig {
  method: "get" | "post" | "put" | "delete";
  url: string;
}

const apiClient = axios.create({
  baseURL: API_URL,
});

// Add Authorization headers for each call api
apiClient.interceptors.request.use((config) => {
  const token = localStorage.getItem("access_token");
  if (token) {
    config.headers.set("Authorization", `Bearer ${token}`);
  }
  return config;
});

// Check token validity for each call api dans redirect to /login if invalid
// apiClient.interceptors.response.use(
//     (response) => response,
//     (error) => {
//         const isLoginRequest = error.config?.url === "/api/login"
//       if (error.response && error.response.status === 401 && !isLoginRequest) {
//         localStorage.removeItem("access_token");
//         window.location.href = "/login";
//       }
//       return Promise.reject(error);
//     }
//   );

const fetchRequest = async <T>({
  method,
  url,
  headers,
  data,
}: FetchRequest): Promise<T> => {
  try {
    const response = await apiClient.request<T>({
      method,
      url,
      headers,
      data,
    });
    return response.data;
  } catch (error) {
    if (error instanceof AxiosError) {
      const errorMessage =
        error.response?.data?.error?.message || "Une erreur s'est produite";
      console.error(`API request failed: ${errorMessage}`);
    } else {
      console.error("An unexpected error occurred:", error);
    }
    throw error;
  }
};

export default fetchRequest;
