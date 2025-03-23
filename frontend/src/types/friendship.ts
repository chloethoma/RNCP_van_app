import { FriendshipUser } from "./user";

export interface Friendship {
    requester: FriendshipUser;
    receiver: FriendshipUser;
    isConfirmed: boolean;
}
