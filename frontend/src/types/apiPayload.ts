import { Spot } from "./spot";
import { User } from "./user";

// 🔹 Spot types
export type SpotFormData = Pick<Spot, "longitude" | "latitude" | "description">;

// 🔹 User types
export type LoginCredentials = Pick<User, "email" | "password">;
export type RegistrationCredentials = Pick<User, "email" | "pseudo" | "password">;
export type Token = {
  token: string;
};
export type PasswordUpdateCredentials = {
  currentPassword: string,
  newPassword: string
}

// 🔹 Friendship types
export type PendingFriendshipType = "received" | "sent";
