import fetchRequest from "./apiClient";
import { SpoGeoJsonCollection, Spot, SpotGeoJson } from "../../types/spot";
import { FriendshipUser, User } from "../../types/user";
import { Friendship } from "../../types/friendship";

// 🔹 Spot types
type SpotFormData = Pick<Spot, "longitude" | "latitude" | "description">;

// 🔹 User types
type LoginCredentials = Pick<User, "email" | "password">;
type RegistrationCredentials = Pick<User, "email" | "pseudo" | "password">;
type Token = {
  token: string;
};
type PasswordUpdateCredentials = {
  currentPassword: string,
  newPassword: string
}

// 🔹 Friendship types
type FriendshipCreatePayload = {
  receiver: Pick<FriendshipUser, "id">
};

type PendingFriendshipType = "received" | "sent";


// =====================================
// 📌 SPOT REQUESTS
// =====================================

export const fetchSpots = async (): Promise<SpotGeoJson[]> => {
  const response = await fetchRequest<SpoGeoJsonCollection>({
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

// =====================================
// 📌 USER REQUESTS
// =====================================

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
    url: "api/users",
  });
};

export const updateUser = async (userData: User): Promise<User> => {
  return await fetchRequest<User>({
    method: "put",
    url: "/api/users",
    data: userData,
  });
};

export const updateUserPassword = async (credentials: PasswordUpdateCredentials): Promise<void> => {
  await fetchRequest<PasswordUpdateCredentials>({
    method: "patch",
    url: "/api/users",
    data: credentials,
  })
}

export const deleteUser = async (): Promise<void> => {
  await fetchRequest<void>({
    method: "delete",
    url: "/api/users",
  });

  localStorage.removeItem("access_token");
};

// =====================================
// 📌 FRIENDSHIP REQUESTS
// =====================================

export const searchUserByPseudo = async (pseudo: string): Promise<FriendshipUser[]> => {
  return await fetchRequest<FriendshipUser[]>({
    method: "get",
    url: `/api/search/users?pseudo=${encodeURIComponent(pseudo)}`,
  })
}

export const createFriendshipRequest = async (payload: FriendshipCreatePayload): Promise<Friendship> => {
  return await fetchRequest<Friendship>({
    method:"post",
    url: "/api/friendships",
    data: payload,
  })
}

export const getPendingFriendships = async (type: PendingFriendshipType): Promise<Friendship[]> => {
  return await fetchRequest<Friendship[]>({
    method: "get",
    url: `/api/friendships/pending/${type}`,
  })
}

export const getConfirmedFriendships = async (): Promise<Friendship[]> => {
  return await fetchRequest<Friendship[]>({
    method: "get",
    url: "/api/friendships/confirmed",
  })
}

export const acceptFriendship = async (friendId: number): Promise<Friendship> => {
  return await fetchRequest<Friendship>({
    method: "patch",
    url: `/api/friendships/${friendId}/confirm`
  })
}

export const deleteFriendship = async (friendId: number): Promise<void> => {
  return await fetchRequest<void>({
    method: "delete",
    url: `/api/friendships/${friendId}`
  })
}