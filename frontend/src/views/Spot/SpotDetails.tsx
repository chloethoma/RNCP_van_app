import { useLocation, useNavigate, useParams } from "react-router";
import { Spot } from "../../types/spot";
import { useEffect, useState } from "react";
import ErrorMessage from "../../components/ErrorMessage";
import Button from "../../components/buttons/Button";
import {
  Heart,
  Navigation,
  PencilLine,
  Share2,
  Trash,
} from "lucide-react";
import Header from "../../components/header/Header";
import { deleteSpot, fetchSpotById } from "../../services/api/apiRequests";
import SuccessMessage from "../../components/SuccessMessage";

const MESSAGES = {
  ERROR_DEFAULT: "Une erreur est survenue",
  ERROR_FETCH_SPOT: "Erreur lors de a récupération du spot",
  SUCCESS_DELETE: "Spot supprimé avec succès !"
};

function SpotDetails() {
  const { spotId } = useParams<{ spotId: string }>();
  const navigate = useNavigate();
  const location = useLocation();

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
            setErrorMessage(MESSAGES.ERROR_FETCH_SPOT);
          }
        }
      };

      fetchSpot();
    }
  }, [spotId, spot]);

  const handleEdit = () => {
    navigate(`/spot/${spot?.id}/edit`, { state: { spot } });
  };

  const handleDelete = async () => {
    try {
      await deleteSpot(Number(spotId));
      navigate('/', {state: {successMessage: MESSAGES.SUCCESS_DELETE}});
    } catch (error) {
      if (error instanceof Error) {
        setErrorMessage(error.message);
      } else {
        setErrorMessage(MESSAGES.ERROR_DEFAULT);
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
      <Header text={"Fiche spot"} />

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
          <h1 className="text-2xl font-bold text-dark">
            Spot #{spot.id}
          </h1>
          <p className="text-grey text-sm mt-2">{spot.description}</p>
        </div>

          <div className="flex justify-between items-center mt-4">
            <div className="flex flex-col items-center gap-2">
              <Button onClick={handleItinerary} icon={<Navigation size={24} />} />
              <span className="mt-1 text-xs text-grey">Itinéraire</span>
            </div>

            <div className="flex flex-col items-center gap-2">
              <Button onClick={handleFavorite} icon={<Heart size={24} />} />
              <span className="mt-1 text-xs text-grey">Favoris</span>
            </div>

            <div className="flex flex-col items-center gap-2">
              <Button onClick={handleShare} icon={<Share2 size={24} />} />
              <span className="mt-1 text-xs text-grey">Partager</span>
            </div>

            <div className="flex flex-col items-center gap-2">
              <Button onClick={handleEdit} icon={<PencilLine size={24} />} />
              <span className="mt-1 text-xs text-grey">Modifier</span>
            </div>

            <div className="flex flex-col items-center gap-2">
              <Button onClick={handleDelete} icon={<Trash size={24} />} color="red" />
              <span className="mt-1 text-xs text-grey">Supprimer</span>
            </div>
          </div>


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
