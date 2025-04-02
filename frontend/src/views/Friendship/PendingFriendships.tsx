import { useEffect, useState } from "react";
import Header from "../../components/header/Header";
import ErrorMessage from "../../components/messages/ErrorMessage";
import { PartialFriendship } from "../../types/friendship";
import {
  acceptFriendship,
  deleteFriendship,
  getPendingFriendshipList,
} from "../../services/api/apiRequests";
import { FriendshipUser } from "../../types/user";
import ListButton from "../../components/buttons/ListButton";
import FriendshipUserRow from "../../components/friendshipList/FriendshipUserRow";

const MESSAGES = {
  ERROR_DEFAULT: "Une erreur est survenue",
};

function PendingFriendships() {
  const [errorMessage, setErrorMessage] = useState<string | null>(null);
  const [loading, setLoading] = useState<boolean>(false);
  const [viewFriendshipsReceived, setViewFriendshipsReceived] = useState<boolean>(true);
  const [friendshipList, setFriendshipList] = useState<PartialFriendship[]>([]);

  useEffect(() => {
    const fetchPendingFriendshipList = async () => {
      setLoading(true);

      try {
        const type = viewFriendshipsReceived ? "received" : "sent";
        const data = await getPendingFriendshipList(type);
        setFriendshipList(data);
      } catch (error) {
        setErrorMessage(
          error instanceof Error ? error.message : MESSAGES.ERROR_DEFAULT
        );
      } finally {
        setLoading(false);
      }
    };

    fetchPendingFriendshipList();
  }, [viewFriendshipsReceived]);

  const handleAcceptFriendship = async (friendId: number) => {
    try {
      await acceptFriendship(friendId);

      setFriendshipList((prevFriendships) =>
        prevFriendships.filter((friendship) => {
          return friendship.friend.id !== friendId;
        })
      );

    } catch (error) {
      setErrorMessage(
        error instanceof Error ? error.message : MESSAGES.ERROR_DEFAULT
      );
    }
  };

  const handleDeleteFriendship = async (friendId: number) => {
    try {
      await deleteFriendship(friendId);

      setFriendshipList((prevFriendships) =>
        prevFriendships.filter((friendship) => {
          return friendship.friend.id !== friendId;
        })
      );

    } catch (error) {
      setErrorMessage(
        error instanceof Error ? error.message : MESSAGES.ERROR_DEFAULT
      );
    }
  };

  return (
    <div className="flex flex-col items-center w-full min-h-screen bg-light-grey font-default">
      <Header text="DEMANDES EN ATTENTE" />

      <ErrorMessage
        errorMessage={errorMessage}
        setErrorMessage={setErrorMessage}
      />

      {/* Toggle */}
      <div className="w-full flex justify-center mt-4">
        <div className="flex bg-white px-2 py-1 rounded-full shadow-md">
          <button
            onClick={() => setViewFriendshipsReceived(true)}
            className={`px-4 py-2 text-md font-semibold rounded-full transition ${
              viewFriendshipsReceived ? "bg-dark-green text-white" : "text-grey"
            }`}
          >
            Demandes reçues
          </button>
          <button
            onClick={() => setViewFriendshipsReceived(false)}
            className={`px-4 py-2 text-md font-semibold rounded-full transition ${
              !viewFriendshipsReceived
                ? "bg-dark-green text-white"
                : "text-grey"
            }`}
          >
            Demandes envoyées
          </button>
        </div>
      </div>

      {/* Loader */}
      {loading && <p className="mt-4 text-grey">Chargement...</p>}

      {/* User list */}
      {!loading && (
        <ul className="w-full max-w-md mt-4 space-y-2">
          {friendshipList.length > 0 ? (
            friendshipList.map((friendship) => {
              const user: FriendshipUser = friendship.friend;

              return (
                <FriendshipUserRow user={user}>
                  {viewFriendshipsReceived ? (
                    <>
                      <ListButton
                        onClick={() => handleAcceptFriendship(user.id)}
                        label="Accepter"
                        color="darkGreen"
                      />
                      <ListButton
                        onClick={() => handleDeleteFriendship(user.id)}
                        label="Refuser"
                        color="red"
                      />
                    </>
                  ) : (
                    <ListButton
                      onClick={() => handleDeleteFriendship(user.id)}
                      label="Annuler"
                      color="red"
                    />
                  )}
                </FriendshipUserRow>
              );
            })
          ) : (
            <p className="text-grey mt-4">
              {viewFriendshipsReceived
                ? "Aucune demande reçue"
                : "Aucune demande envoyée"}
            </p>
          )}
        </ul>
      )}
    </div>
  );
}

export default PendingFriendships;
