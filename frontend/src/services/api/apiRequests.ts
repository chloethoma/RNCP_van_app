import fetchRequest from "./apiClient";
import { Feature, FeatureCollection } from "../../types/feature";
import { Spot } from "../../types/spot";

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

type SpotFormData = Pick<Spot, "longitude" | "latitude" | "description">

export const fetchSpots = async (): Promise<Feature[]> => {
  const response = await fetchRequest<FeatureCollection>({
    method: "get",
    url: "api/spots",
  });

  return response.features;
};

export const fetchSpotById = async (spotId: number): Promise<Spot> => {
  return await fetchRequest<Spot>({
    method: "get",
    url: `api/spots/${spotId}`,
  });
};

export const loginUser = async (credentials: LoginCredentials): Promise<LoginResponse> => {
  const response = await fetchRequest<LoginResponse>({
    method: "post",
    url: "/api/login",
    data: credentials,
  });

  localStorage.setItem("access_token", response.token);

  return response;
}

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

export const createSpot = async (spotData: SpotFormData): Promise<Spot> => {
  return await fetchRequest<Spot>({
    method:"post",
    url:"/api/spots",
    data: spotData
  })
};

export const updateSpot = async(spotData: Spot): Promise<Spot> => {
  return await fetchRequest<Spot>({
    method:"put",
    url:`/api/spots/${spotData.id}`,
    data: spotData
  })
}

export const deleteSpot = async(spotId: number): Promise<void> => {
  return await fetchRequest<void>({
    method:"delete",
    url:`/api/spots/${spotId}`
  })
}