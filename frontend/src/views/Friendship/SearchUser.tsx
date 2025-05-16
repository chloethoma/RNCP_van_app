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
import FriendshipUserRow from "../../components/FriendshipUserRow";
import { messages } from "../../services/helpers/messagesHelper";

function SearchUser() {
  const [query, setQuery] = useState("");
  const [users, setUsers] = useState<FriendshipUser[]>([]);
  const [loading, setLoading] = useState(false);
  const [hasSearched, setHasSearched] = useState(false);
  const [errorMessage, setErrorMessage] = useState<string | null>(null);
  const [requestsSent, setRequestsSent] = useState<{ [key: number]: boolean }>(
    {},
  );

  useEffect(() => {
    if (!query) {
      setUsers([]);
      return;
    }

    const fetchUsers = async () => {
      setLoading(true);
      setErrorMessage("");
      setHasSearched(false);
      try {
        const userList = await searchUserByPseudo(query);
        setUsers(userList);
      } catch (error) {
        setErrorMessage(
          error instanceof Error ? error.message : messages.error_default,
        );
      } finally {
        setLoading(false);
        setHasSearched(true);
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
        error instanceof Error ? error.message : messages.error_default,
      );
    }
  };

  const handleCancelRequest = async (userId: number) => {
    try {
      await deleteFriendship(userId);
      setRequestsSent((prev) => ({ ...prev, [userId]: false }));
    } catch (error) {
      setErrorMessage(
        error instanceof Error ? error.message : messages.error_default,
      );
    }
  };

  return (
    <>
      <Header text="RECHERCHE" />
      <div className="flex flex-col items-center p-2 min-h-screen bg-light-grey font-default">
        <ErrorMessage
          errorMessage={errorMessage}
          setErrorMessage={setErrorMessage}
        />

        <div className="relative w-full flex flex-col items-center max-w-lg rounded-xl h-[calc(100vh-4rem-6rem)] md:p-4">
          {/* Search friends */}
          <div className="w-full flex items-center border-light-grey rounded-md shadow-md bg-white">
            <Search size={20} color="gray" className="ml-3" />
            <input
              type="text"
              value={query}
              onChange={(e) => setQuery(e.target.value)}
              placeholder="Rechercher un utilisateur..."
              className="w-full px-3 py-2 border-none focus:outline-none"
            />
          </div>

          {/* Loading */}
          {loading && <p className="mt-4 text-grey">Chargement...</p>}

          {/* User list */}
          <ul className="w-full mt-4 space-y-2">
            {users.length > 0 &&
              users.map((user) => (
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
              ))}
          </ul>

          {hasSearched && users.length === 0 && !loading && (
            <p className="text-grey mt-4">Aucun utilisateur trouvé</p>
          )}
        </div>
      </div>
    </>
  );
}

export default SearchUser;
