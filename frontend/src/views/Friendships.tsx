import { useEffect, useState } from "react";
import { Search, UserPlus } from "lucide-react";
import Header from "../components/header/Header";
import IconButton from "../components/buttons/IconButton";
import { Link, useNavigate } from "react-router";
import { PartialFriendship, ReceivedFriendshipNumber } from "../types/friendship";
import {
  deleteFriendship,
  getConfirmedFriendshipList,
  getReceivedFrienshipSummary,
} from "../services/api/apiRequests";
import FriendshipUserRow from "../components/friendshipList/FriendshipUserRow";
import ListButton from "../components/buttons/ListButton";
import ErrorMessage from "../components/messages/ErrorMessage";

const MESSAGES = {
  ERROR_DEFAULT: "Une erreur est survenue",
};

const Friendships = () => {
  const navigate = useNavigate();
  const [errorMessage, setErrorMessage] = useState<string | null>(null);
  const [searchQuery, setSearchQuery] = useState("");
  const [friendshipList, setFriendshipList] = useState<PartialFriendship[]>([]);
  const [receivedFriendshipNumber, setReceivedFriendshipNumber] = useState<number>(0);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    const fetchFriendships = async () => {
      setLoading(true);

      try {
        const friendList = await getConfirmedFriendshipList();
        setFriendshipList(friendList);
      } catch (error) {
        setErrorMessage(
          error instanceof Error ? error.message : MESSAGES.ERROR_DEFAULT
        );
      } finally {
        setLoading(false);
      }
    };

    fetchFriendships();
  }, []);

  useEffect(() => {
    const fetchReceivedFriendshipsSummary = async () => {
      try {
        const summary = await getReceivedFrienshipSummary();
        setReceivedFriendshipNumber(summary.count);
      } catch (error) {
        setErrorMessage(
          error instanceof Error ? error.message : MESSAGES.ERROR_DEFAULT
        );
      }
    }

    fetchReceivedFriendshipsSummary();
  }, []);

  const handleDeleteFriend = async (friendId: number) => {
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
      <Header text="MA COMMU" />

      <ErrorMessage
        errorMessage={errorMessage}
        setErrorMessage={setErrorMessage}
      />

      {/* Pending friendships */}
      <div className="w-full flex justify-between items-center p-4 bg-white mt-4 shadow-md text-black">
        <Link
          to={"/friendships/pending"}
          className="text-md font-semibold text-black"
        >
          Demandes en attente de validation : {receivedFriendshipNumber}
        </Link>
      </div>

      {/* Search friends */}
      <div className="w-full flex items-center justify-between p-4 bg-white mt-4 shadow-md">
        <div className="flex items-center border rounded-md w-3/4">
          <Search size={20} color="gray" className="ml-3" />
          <input
            type="text"
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            placeholder="Rechercher un ami..."
            className="w-full px-3 py-2 border-none focus:outline-none"
          />
        </div>

        {/* Add new friend */}
        <IconButton
          icon={<UserPlus size={24} strokeWidth={3} />}
          onClick={() => navigate("/search/users")}
          color="darkGreen"
          className="ml-4"
        />
      </div>

      {/* Friend list */}
      {!loading && (
      <ul className="w-full max-w-md mt-4 space-y-2">
        {friendshipList.length > 0 ? (
          friendshipList
            .filter((friendship) => {
              return friendship.friend.pseudo
                .toLowerCase()
                .includes(searchQuery.toLowerCase());
            })
            .map((friendship) => {
              const friend = friendship.friend

              return (
                <FriendshipUserRow key={friend.id} user={friend}>
                  <ListButton
                    onClick={() => handleDeleteFriend(friend.id)}
                    label="Supprimer"
                    color="red"
                  />
                </FriendshipUserRow>
              );
            })
        ) : (
          <p className="text-grey mt-4">Aucune amitié trouvée</p>
        )}
      </ul>
      )}
    </div>
  );
};

export default Friendships;
