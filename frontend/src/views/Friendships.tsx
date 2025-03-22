import { useState } from "react";
import { Search, UserPlus } from "lucide-react";
import Header from "../components/header/Header";
import IconButton from "../components/buttons/IconButton";
import { Link, useNavigate } from "react-router";

const Friendships = () => {
  const navigate = useNavigate();
  const [searchQuery, setSearchQuery] = useState("");
  // const [friends, setFriends] = useState<User[]>([]);
  // const [loading, setLoading] = useState(false);

  // useEffect(() => {
  //   const fetchData = async () => {
  //     setLoading(true);
  //     try {
  //       const data = await fetchFriends(); // Requête pour récupérer les amis
  //       setFriends(data);
  //     } catch (error) {
  //       console.error("Erreur de récupération des amis:", error);
  //     } finally {
  //       setLoading(false);
  //     }
  //   };
  //   fetchData();
  // }, []);

  // const handleDeleteFriend = async (id: number) => {
  //   try {
  //     await removeFriend(id); // Fonction pour supprimer un ami
  //     setFriends(friends.filter(friend => friend.id !== id)); // Mise à jour de la liste après suppression
  //   } catch (error) {
  //     console.error("Erreur de suppression de l'ami:", error);
  //   }
  // };

  // const filteredFriends = friends.filter(friend =>
  //   friend.pseudo.toLowerCase().includes(searchQuery.toLowerCase())
  // );

  return (
    <div className="flex flex-col items-center w-full min-h-screen bg-light-grey font-default">
      <Header text="MA COMMU" />

      {/* Pending friendships */}
      <div className="w-full flex justify-between items-center p-4 bg-white mt-4 shadow-md text-black">
        <Link to={"/friendships/pending"} className="text-md font-semibold text-black">Demandes en attente de validation</Link>
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

      {/* Liste des amis */}
      {/* {loading ? (
        <p>Chargement...</p>
      ) : (
        <div className="w-full p-4">
          {filteredFriends.length > 0 ? (
            filteredFriends.map((friend) => (
              <div key={friend.id} className="flex items-center justify-between bg-white p-3 mt-2 rounded-md shadow-md">
                <img
                  src={friend.avatar || "/default-avatar.png"}
                  alt={friend.pseudo}
                  className="w-12 h-12 rounded-full"
                />
                <p className="flex-1 ml-4 text-lg font-semibold">{friend.pseudo}</p>
                <IconButton
                  icon={<Trash2 size={20} color="red" />}
                  onClick={() => handleDeleteFriend(friend.id)}
                  color="white"
                />
              </div>
            ))
          ) : (
            <p>Aucun ami trouvé.</p>
          )}
        </div>
      )} */}
    </div>
  );
};

export default Friendships;
