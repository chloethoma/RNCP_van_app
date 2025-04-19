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
        className="flex items-center justify-between bg-white p-3 rounded-lg shadow-md"
      >
        <div className="flex items-center space-x-3">
          <img
            src={user.picture || Avatar}
            alt={user.pseudo}
            className="w-10 h-10 rounded-full"
          />
          <div className="text-black font-medium">{user.pseudo}</div>
        </div>

        {/* Buttons */}
        <div className="flex items-center space-x-2">{children}</div>
      </li>
    </>
  );
}

export default FriendshipUserRow;
