import { useLocation, useNavigate, useParams } from "react-router";
import { Spot } from "../../types/spot";
import { useContext, useEffect, useState } from "react";
import ErrorMessage from "../../components/messages/ErrorMessage";
import IconButton from "../../components/buttons/IconButton";
import {
  Heart,
  MapPin,
  Navigation,
  PencilLine,
  Share2,
  Trash,
} from "lucide-react";
import Header from "../../components/headers/Header";
import { deleteSpot, fetchSpotById } from "../../services/api/apiRequests";
import SuccessMessage from "../../components/messages/SuccessMessage";
import UserContext from "../../hooks/UserContext";
import { messages } from "../../services/helpers/messagesHelper";
import ConfirmationModal from "../../components/modal/ConfirmationModal";

function SpotDetails() {
  const { spotId } = useParams<{ spotId: string }>();
  const navigate = useNavigate();
  const location = useLocation();
  const context = useContext(UserContext);
  const [isDeleteSpotModalOpen, setIsDeleteSpotModalOpen] = useState(false);

  if (!context) {
    // Gérer le cas où le contexte n’est pas défini, ou afficher une erreur
    throw new Error("UserContext must be used within a UserProvider");
  }

  const { user } = context;

  const [errorMessage, setErrorMessage] = useState<string | null>(null);
  const [successMessage, setSuccessMessage] = useState<string | null>(
    location.state?.successMessage || null,
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
      navigate("/", {
        state: { successMessage: messages.success_spot_delete },
      });
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
    <>
      <Header text={"FICHE SPOT"} />
      <div className="flex flex-col items-center p-2 min-h-screen bg-light-grey font-default">
        <ErrorMessage
          errorMessage={errorMessage}
          setErrorMessage={setErrorMessage}
        />
        <SuccessMessage
          successMessage={successMessage}
          setSuccessMessage={setSuccessMessage}
        />

        {/* Pictures section */}
        {spot && (
          <div className="w-full max-w-lg bg-white shadow-lg rounded-xl px-6 py-3 relative">
            <div className="h-40 border-2 border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-sm rounded-lg mb-4">
              <p>Zone d'affichage des images (à implémenter)</p>
            </div>

            {/* Action buttons section */}
            {user && user.id === spot.owner.id && (
              <div className="flex justify-between items-center p-2 my-2">
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
                  <IconButton
                    onClick={handleShare}
                    icon={<Share2 size={24} />}
                  />
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
                    onClick={() => setIsDeleteSpotModalOpen(true)}
                    icon={<Trash size={24} />}
                    color="red"
                  />
                  <span className="mt-1 text-xs text-grey">Supprimer</span>
                </div>
              </div>
            )}

            {/* Modal for confirmation delete account */}
            {isDeleteSpotModalOpen && (
              <ConfirmationModal
                title="Êtes-vous sûr de vouloir supprimer ce spot ?"
                onConfirm={handleDelete}
                onCancel={() => setIsDeleteSpotModalOpen(false)}
                confirmText="Oui, supprimer"
                cancelText="Annuler"
              />
            )}

            {/* Details section */}
            <div className="p-2 space-y-3">
              <h1 className="text-2xl font-bold text-dark">Description</h1>
              <p className="text-grey text-sm mt-2">{spot.description}</p>
            </div>

            {/* Coordinates section */}
            <div className="flex flex-row gap-2 my-4">
              <MapPin size={24} className="text-red drop-shadow-lg" />
              <p className="text-xs text-grey mt-2">
                Latitude: {spot.latitude} | Longitude: {spot.longitude}
              </p>
            </div>
          </div>
        )}
      </div>
    </>
  );
}

export default SpotDetails;
