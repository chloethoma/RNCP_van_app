import axios, { AxiosError, AxiosRequestConfig } from "axios";
import { translateErrorMessage } from "../helpers/errorApiHelper";
import { messages } from "../helpers/messagesHelper";

const API_URL = import.meta.env.VITE_API_URL;

interface FetchRequest extends AxiosRequestConfig {
  method: "get" | "post" | "put" | "patch" |"delete";
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
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    const isLoginRequest = error.config?.url === "/api/login";
    if (error.response && error.response.status === 401 && !isLoginRequest) {
      localStorage.removeItem("access_token");
      window.location.href = "/login";
    }
    return Promise.reject(error);
  },
);

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
    let errorMessage = messages.error_default;

    if (error instanceof AxiosError && error.response?.data?.error) {
      errorMessage = translateErrorMessage(error.response.data.error);
    }

    throw new Error(errorMessage);
  }
};

export default fetchRequest;
