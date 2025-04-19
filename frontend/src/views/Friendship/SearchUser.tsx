import { useState, useEffect } from "react";
import {
  createFriendshipRequest,
  deleteFriendship,
  searchUserByPseudo,
} from "../../services/api/apiRequests";
import { FriendshipUser } from "../../types/user";
import Header from "../../components/headers/Header";
import { Search } from "lucide-react";
import ErrorMessage from "../../components/messages/ErrorMessage";
import ListButton from "../../components/buttons/ListButton";
import FriendshipUserRow from "../../components/friendshipList/FriendshipUserRow";
import { messages } from "../../services/helpers/messagesHelper";

function SearchUser() {
  const [query, setQuery] = useState("");
  const [users, setUsers] = useState<FriendshipUser[]>([]);
  const [loading, setLoading] = useState(false);
  const [errorMessage, setErrorMessage] = useState<string | null>(null);
  const [requestsSent, setRequestsSent] = useState<{ [key: number]: boolean }>(
    {}
  );

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
          error instanceof Error ? error.message : messages.error_default
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

  const handleFriendshipRequest = async (userId: number) => {
    try {
      await createFriendshipRequest(userId);
      setRequestsSent((prev) => ({ ...prev, [userId]: true }));
    } catch (error) {
      setErrorMessage(
        error instanceof Error ? error.message : messages.error_default
      );
    }
  };

  const handleCancelRequest = async (userId: number) => {
    try {
      await deleteFriendship(userId);
      setRequestsSent((prev) => ({ ...prev, [userId]: false }));
    } catch (error) {
      setErrorMessage(
        error instanceof Error ? error.message : messages.error_default
      );
    }
  };

  return (
    <div className="flex flex-col items-center w-full min-h-screen bg-light-grey font-default">
      <Header text="RECHERCHE" />

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
            placeholder="Rechercher un utilisateur..."
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
              <FriendshipUserRow key={user.id} user={user}>
                {requestsSent[user.id] ? (
                  <>
                    <ListButton
                      label="Demande envoyée"
                      color="grey"
                      className="disabled"
                    />
                    <ListButton
                      onClick={() => handleCancelRequest(user.id)}
                      label="Annuler"
                      color="red"
                    />
                  </>
                ) : (
                  <ListButton
                    onClick={() => handleFriendshipRequest(user.id)}
                    label="Ajouter"
                    color="darkGreen"
                  />
                )}
              </FriendshipUserRow>
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
