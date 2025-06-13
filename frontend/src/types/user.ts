export interface User {
  id: number;
  email: string;
  emailVerified: boolean;
  pseudo: string;
  createdAt: string;
  updatedAt: string;
  picture: string | null;
  token: string | null;
  password?: string;
}

export interface UserSummary {
  friendsNumber: number;
  spotsNumber: number;
}

export type FriendshipUser = Pick<User, "id" | "pseudo" | "picture">;
