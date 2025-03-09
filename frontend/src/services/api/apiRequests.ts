import fetchRequest from "./apiClient";
import { Feature, FeatureCollection } from "../../types/feature";
import { Spot } from "../../types/spot";
import { SearchUserResult, User } from "../../types/user";

// Spot
type SpotFormData = Pick<Spot, "longitude" | "latitude" | "description">;

// User
type LoginCredentials = Pick<User, "email" | "password">;
type RegistrationCredentials = Pick<User, "email" | "pseudo" | "password">;
type Token = {
  token: string;
};

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

export const createSpot = async (spotData: SpotFormData): Promise<Spot> => {
  return await fetchRequest<Spot>({
    method: "post",
    url: "/api/spots",
    data: spotData,
  });
};

export const updateSpot = async (spotData: Spot): Promise<Spot> => {
  return await fetchRequest<Spot>({
    method: "put",
    url: `/api/spots/${spotData.id}`,
    data: spotData,
  });
};

export const deleteSpot = async (spotId: number): Promise<void> => {
  return await fetchRequest<void>({
    method: "delete",
    url: `/api/spots/${spotId}`,
  });
};

export const loginUser = async (
  credentials: LoginCredentials,
): Promise<Token> => {
  const response = await fetchRequest<Token>({
    method: "post",
    url: "/api/login",
    data: credentials,
  });

  localStorage.setItem("access_token", response.token);

  return response;
};

export const registerUser = async (
  credentials: RegistrationCredentials,
): Promise<Token> => {
  const response = await fetchRequest<Token>({
    method: "post",
    url: "/register",
    data: credentials,
  });

  localStorage.setItem("access_token", response.token);

  return response;
};

export const fetchUserByToken = async (): Promise<User> => {
  return await fetchRequest<User>({
    method: "get",
    url: "api/user",
  });
};

export const updateUser = async (userData: User): Promise<User> => {
  return await fetchRequest<User>({
    method: "put",
    url: "/api/user",
    data: userData,
  });
};

export const deleteUser = async (): Promise<void> => {
  await fetchRequest<void>({
    method: "delete",
    url: "/api/user",
  });

  localStorage.removeItem("access_token");
};

export const searchUserByPseudo = async (pseudo: string): Promise<SearchUserResult[]> => {
  return await fetchRequest<SearchUserResult[]>({
    method: "get",
    url: `/api/search/user?pseudo=${encodeURIComponent(pseudo)}`,
  })
}
