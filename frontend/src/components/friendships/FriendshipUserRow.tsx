import { FriendshipUser } from "../../types/user";
import Avatar from "../../assets/avatar_cat.png";

interface FriendshipUserRowProps {
  user: FriendshipUser;
  children: React.ReactNode;
}

function FriendshipUserRow({ user, children }: FriendshipUserRowProps) {
  return (
    <>
      <li
        key={user.id}
        className="flex items-center justify-between bg-white p-2 rounded-lg shadow-sm"
      >
        <div className="flex items-center space-x-3">
          <img
            src={user.picture || Avatar}
            alt={user.pseudo}
            className="w-10 h-10 rounded-full"
          />
          <div className="text-sm font-semibold">{user.pseudo}</div>
        </div>

        {/* Buttons */}
        <div className="flex items-center space-x-2">{children}</div>
      </li>
    </>
  );
}

export default FriendshipUserRow;
