import { MapPin } from "lucide-react";
import { useLocation, useNavigate } from "react-router";
import { useId, useState } from "react";
import Header from "../../components/header/Header";
import FormButton from "../../components/buttons/FormButton";
import ErrorMessage from "../../components/ErrorMessage";
import { createSpot } from "../../services/api/apiRequests";

const MESSAGES = {
  ERROR_DEFAULT: "Une erreur est survenue",
  ERROR_INPUT_MISSING: "Veuillez entrer une description",
  ERROR_CREATE: "Erreur lors de la création du spot",
  SUCCESS_CREATE: "Spot ajouté à la map avec succès !",
};

function SpotAddDetails() {
  const location = useLocation();
  const navigate = useNavigate();
  const id = useId();

  const { longitude, latitude } = location.state;
  const [description, setDescription] = useState<string>("");
  const [errorMessage, setErrorMessage] = useState<string | null>(null);

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();

    if (!description.trim()) {
      setErrorMessage(MESSAGES.ERROR_INPUT_MISSING);
      return;
    }

    try {
      const requestBody = { longitude, latitude, description };
      await createSpot(requestBody);

      navigate("/", { state: { successMessage: MESSAGES.SUCCESS_CREATE } });
    } catch (error) {
      if (error instanceof Error) {
        setErrorMessage(error.message);
      } else {
        setErrorMessage(MESSAGES.ERROR_DEFAULT);
      }
    }
  };

  return (
    <div className="relative flex flex-col items-center justify-start min-h-screen bg-light-grey w-full">
      <Header text={"Ajout d'un spot"} />

      <ErrorMessage
        errorMessage={errorMessage}
        setErrorMessage={setErrorMessage}
      />

      <div
        className="w-full max-w-lg bg-white shadow-lg rounded-xl p-6 relative 
                   overflow-y-auto h-[calc(100vh-4rem-5rem)]"
      >
        <div className="h-36 border-2 border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-sm rounded-lg mb-4">
          <p>Zone d'upload d'images (à implémenter)</p>
        </div>

        <div className="text-center mb-4">
          <MapPin size={26} className="text-red drop-shadow-lg mx-auto" />
          <p className="text-xs text-grey mt-2">
            Latitude: {latitude} | Longitude: {longitude}
          </p>
        </div>

        <form onSubmit={handleSubmit}>
          <div className="mt-3">
            <label htmlFor={id} className="text-sm font-medium text-dark-grey">
              Description
            </label>
            <textarea
              id={id}
              className="w-full h-32 p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-dark-green placeholder-top placeholder-left resize-none"
              placeholder="Ajoutez une description..."
              value={description}
              onChange={(e) => setDescription(e.target.value)}
            />
          </div>

          <FormButton>Enregistrer</FormButton>
        </form>
      </div>
    </div>
  );
}

export default SpotAddDetails;
