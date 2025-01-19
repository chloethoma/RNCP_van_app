import fetchRequest from "./apiClient";
import { Feature, FeatureCollection } from "../../types/feature";

interface LoginCredentials {
  email: string;
  password: string;
}

interface LoginResponse {
  token: string;
}

interface RegistrationCredentials {
  email: string;
  pseudo: string;
  password: string;
}

interface RegistrationResponse {
  token: string;
}

export const fetchSpots = async (): Promise<Feature[]> => {
  const response = await fetchRequest<FeatureCollection>({
    method: "get",
    url: "api/spots",
  });

  return response.features;
};

export const loginUser = async (
  credentials: LoginCredentials
): Promise<LoginResponse> => {
  const response = await fetchRequest<LoginResponse>({
    method: "post",
    url: "/api/login",
    data: credentials,
  });
  localStorage.setItem("access_token", response.token);
  return response;
};

export const registerUser = async (
  credentials: RegistrationCredentials
): Promise<RegistrationResponse> => {
  const response = await fetchRequest<RegistrationResponse>({
    method: "post",
    url: "/register",
    data: credentials,
  });
  localStorage.setItem("access_token", response.token);
  return response;
};

// export const createSpot = async (spotData: Partial<Feature>): Promise<Feature> => {
//   return await fetchRequest<Feature>("post", "/api/spots", spotData);
// };

// export const deleteSpot = async (spotId: string): Promise<void> => {
//   return await fetchRequest<void>("delete", `/api/spots/${spotId}`);
// };
