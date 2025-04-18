import { useLocation, useNavigate, useParams } from "react-router";
import { Spot } from "../../types/spot";
import { useContext, useEffect, useState } from "react";
import ErrorMessage from "../../components/messages/ErrorMessage";
import IconButton from "../../components/buttons/IconButton";
import { Heart, Navigation, PencilLine, Share2, Trash } from "lucide-react";
import Header from "../../components/headers/Header";
import { deleteSpot, fetchSpotById } from "../../services/api/apiRequests";
import SuccessMessage from "../../components/messages/SuccessMessage";
import UserContext from "../../hooks/UserContext";
import { messages } from "../../services/helpers/messagesHelper";

function SpotDetails() {
  const { spotId } = useParams<{ spotId: string }>();
  const navigate = useNavigate();
  const location = useLocation();
  const { user } = useContext(UserContext);

  const [errorMessage, setErrorMessage] = useState<string | null>(null);
  const [successMessage, setSuccessMessage] = useState<string | null>(
    location.state?.successMessage || null
  );
  const [spot, setSpot] = useState<Spot | null>(location.state?.spot || null);

  useEffect(() => {
    if (!spot) {
      const fetchSpot = async () => {
        try {
          const fetchedSpot = await fetchSpotById(Number(spotId));
          setSpot(fetchedSpot);
        } catch (error) {
          if (error instanceof Error) {
            setErrorMessage(error.message);
          } else {
            setErrorMessage(messages.error_spot_load);
          }
        }
      };

      fetchSpot();
    }
  }, [spotId, spot]);

  const handleEdit = () => {
    navigate(`/spots/${spot?.id}/edit`, { state: { spot } });
  };

  const handleDelete = async () => {
    try {
      await deleteSpot(Number(spotId));
      navigate("/", { state: { successMessage: messages.success_spot_delete } });
    } catch (error) {
      if (error instanceof Error) {
        setErrorMessage(error.message);
      } else {
        setErrorMessage(messages.error_default);
      }
    }
  };

  const handleFavorite = () => {
    console.log("Ajout aux favoris", spot?.id);
  };

  const handleShare = () => {
    console.log("Partage du spot", spot?.id);
  };

  const handleItinerary = () => {
    console.log("Itinéraire vers", spot?.id);
  };

  return (
    <div className="relative flex flex-col items-center justify-start min-h-screen bg-light-grey w-full">
      <Header text={"FICHE SPOT"} />

      <ErrorMessage
        errorMessage={errorMessage}
        setErrorMessage={setErrorMessage}
      />
      <SuccessMessage
        successMessage={successMessage}
        setSuccessMessage={setSuccessMessage}
      />

      {spot && (
        <div className="w-full max-w-lg bg-white shadow-lg rounded-xl p-6 relative overflow-y-auto h-[calc(100vh-4rem-6rem)]">
          <div className="h-40 border-2 border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-sm rounded-lg mb-4">
            <p>Zone d'affichage des images (à implémenter)</p>
          </div>

          <div className="pb-6">
            <h1 className="text-2xl font-bold text-dark">Spot #{spot.id}</h1>
            <p className="text-grey text-sm mt-2">{spot.description}</p>
          </div>

          {user.id === spot.owner.id && (
            <div className="flex justify-between items-center mt-4">
              <div className="flex flex-col items-center gap-2">
                <IconButton
                  onClick={handleItinerary}
                  icon={<Navigation size={24} />}
                />
                <span className="mt-1 text-xs text-grey">Itinéraire</span>
              </div>

              <div className="flex flex-col items-center gap-2">
                <IconButton
                  onClick={handleFavorite}
                  icon={<Heart size={24} />}
                />
                <span className="mt-1 text-xs text-grey">Favoris</span>
              </div>

              <div className="flex flex-col items-center gap-2">
                <IconButton onClick={handleShare} icon={<Share2 size={24} />} />
                <span className="mt-1 text-xs text-grey">Partager</span>
              </div>

              <div className="flex flex-col items-center gap-2">
                <IconButton
                  onClick={handleEdit}
                  icon={<PencilLine size={24} />}
                />
                <span className="mt-1 text-xs text-grey">Modifier</span>
              </div>

              <div className="flex flex-col items-center gap-2">
                <IconButton
                  onClick={handleDelete}
                  icon={<Trash size={24} />}
                  color="red"
                />
                <span className="mt-1 text-xs text-grey">Supprimer</span>
              </div>
            </div>
          )}

          {/* <div className="text-center mt-6 pt-4">
            <MapPin size={24} className="text-red drop-shadow-lg mx-auto" />
            <p className="text-xs text-grey mt-2">
              Latitude: {spot.latitude} | Longitude: {spot.longitude}
            </p>
          </div> */}
        </div>
      )}
    </div>
  );
}

export default SpotDetails;
