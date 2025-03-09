import { useState, useEffect } from "react";
import { searchUserByPseudo } from "../services/api/apiRequests";
import { SearchUserResult } from "../types/user";
import Header from "../components/header/Header";
import { Search } from "lucide-react";
import ErrorMessage from "../components/ErrorMessage";
import Avatar from "../assets/avatar_cat.png";

const MESSAGES = {
  ERROR_DEFAULT: "Une erreur est survenue",
};

function SearchUser() {
  const [query, setQuery] = useState("");
  const [users, setUsers] = useState<SearchUserResult[]>([]);
  const [loading, setLoading] = useState(false);
  const [errorMessage, setErrorMessage] = useState<string | null>(null);
  const [requestsSent, setRequestsSent] = useState<{ [key: number]: boolean }>({});

  useEffect(() => {
    if (!query) {
      setUsers([]);
      return;
    }

    const fetchUsers = async () => {
      setLoading(true);
      setErrorMessage("");
      try {
        const userList = await searchUserByPseudo(query);
        setUsers(userList);
      } catch (error) {
        setErrorMessage(
          error instanceof Error ? error.message : MESSAGES.ERROR_DEFAULT
        );
      } finally {
        setLoading(false);
      }
    };

    const timer = setTimeout(() => {
      fetchUsers();
    }, 300);

    return () => clearTimeout(timer);
  }, [query]);

  const handleAddFriend = async (userId: number) => {
    try {
      //   await sendFriendRequest(userId);
      setRequestsSent((prev) => ({ ...prev, [userId]: true }));
    } catch (error) {
      setErrorMessage(
        error instanceof Error ? error.message : MESSAGES.ERROR_DEFAULT
      );
    }
  };

  const handleCancelRequest = async (userId: number) => {
    try {
      //   await cancelFriendRequest(userId);
      setRequestsSent((prev) => ({ ...prev, [userId]: false }));
    } catch (error) {
      setErrorMessage(
        error instanceof Error ? error.message : MESSAGES.ERROR_DEFAULT
      );
    }
  };

  return (
    <div className="flex flex-col items-center w-full min-h-screen bg-light-grey font-default">
      <Header text="Ajouter un ami" />

      <ErrorMessage
        errorMessage={errorMessage}
        setErrorMessage={setErrorMessage}
      />
      {/* Search friends */}
      <div className="w-full flex items-center justify-between p-4 bg-white mt-4 shadow-md">
        <div className="flex items-center border rounded-md w-full">
          <Search size={20} color="gray" className="ml-3" />
          <input
            type="text"
            value={query}
            onChange={(e) => setQuery(e.target.value)}
            placeholder="Rechercher un nouvel ami..."
            className="w-full px-3 py-2 border-none focus:outline-none"
          />
        </div>
      </div>

      {/* Loading */}
      {loading && <p className="mt-4 text-grey">Chargement...</p>}

      {/* User list */}
      <ul className="w-full max-w-md mt-4 space-y-2">
        {users.length > 0
          ? users.map((user) => (
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
                <div className="flex items-center space-x-2">
                  {requestsSent[user.id] ? (
                    <>
                      <button
                        className="px-3 py-1 text-grey bg-light-grey rounded-xl"
                        disabled
                      >
                        Demande envoyée
                      </button>
                      <button
                        className="px-3 py-1 text-white bg-red rounded-xl hover:bg-red-hover"
                        onClick={() => handleCancelRequest(user.id)}
                      >
                        Annuler
                      </button>
                    </>
                  ) : (
                    <button
                      className="px-3 py-1 text-white bg-dark-green rounded-xl hover:bg-dark-green-hover"
                      onClick={() => handleAddFriend(user.id)}
                    >
                      Ajouter
                    </button>
                  )}
                </div>
              </li>
            ))
          : query &&
            !loading && (
              <p className="text-grey mt-4">Aucun utilisateur trouvé</p>
            )}
      </ul>
    </div>
  );
}

export default SearchUser;
