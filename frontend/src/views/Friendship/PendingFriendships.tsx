import { useEffect, useState } from "react";
import ErrorMessage from "../../components/messages/ErrorMessage";
import { PartialFriendship } from "../../types/friendship";
import {
  acceptFriendship,
  deleteFriendship,
  getPendingFriendshipList,
} from "../../services/api/apiRequests";
import { FriendshipUser } from "../../types/user";
import ListButton from "../../components/buttons/ListButton";
import FriendshipUserRow from "../../components/friendships/FriendshipUserRow";
import Toggle from "../../components/toggle/Toggle";
import { messages } from "../../services/helpers/messagesHelper";
import ViewWithHeader from "../../components/headers/ViewWithHeader";

function PendingFriendships() {
  const [errorMessage, setErrorMessage] = useState<string | null>(null);
  const [loading, setLoading] = useState<boolean>(false);
  const [friendshipList, setFriendshipList] = useState<PartialFriendship[]>([]);
  const [viewFriendshipsReceived, setViewFriendshipsReceived] =
    useState<boolean>(true); // toggle

  useEffect(() => {
    const fetchPendingFriendshipList = async () => {
      setLoading(true);

      try {
        const type = viewFriendshipsReceived ? "received" : "sent";
        const data = await getPendingFriendshipList(type);
        setFriendshipList(data);
      } catch (error) {
        setErrorMessage(
          error instanceof Error ? error.message : messages.error_default,
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
        }),
      );
    } catch (error) {
      setErrorMessage(
        error instanceof Error ? error.message : messages.error_default,
      );
    }
  };

  const handleDeleteFriendship = async (friendId: number) => {
    try {
      await deleteFriendship(friendId);

      setFriendshipList((prevFriendships) =>
        prevFriendships.filter((friendship) => {
          return friendship.friend.id !== friendId;
        }),
      );
    } catch (error) {
      setErrorMessage(
        error instanceof Error ? error.message : messages.error_default,
      );
    }
  };

  return (
    <ViewWithHeader text="DEMANDES EN ATTENTE">
      <div className="flex flex-col items-center p-2 min-h-screen bg-light-grey font-default">
        <ErrorMessage
          errorMessage={errorMessage}
          setErrorMessage={setErrorMessage}
        />

        <div className="w-full flex justify-center pt-3 text-sm">
          <Toggle
            options={[
              { label: "Demandes reçues", defaultValue: true },
              { label: "Demandes envoyées", defaultValue: false },
            ]}
            selectedValue={viewFriendshipsReceived}
            onChange={setViewFriendshipsReceived}
          />
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
                  <FriendshipUserRow key={user.id} user={user}>
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
              <p className="text-grey m-6 text-center">
                {viewFriendshipsReceived
                  ? "Aucune demande reçue"
                  : "Aucune demande envoyée"}
              </p>
            )}
          </ul>
        )}
      </div>
    </ViewWithHeader>
  );
}

export default PendingFriendships;
