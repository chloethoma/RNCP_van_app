import { MapPin } from "lucide-react";
import { useLocation, useNavigate } from "react-router";
import { useId, useState } from "react";
import Header from "../../components/header/Header";
import FormButton from "../../components/buttons/FormButton";
import ErrorMessage from "../../components/ErrorMessage";
import { createSpot } from "../../services/api/apiRequests";
import { AxiosError } from "axios";

const ERROR_MESSAGES = {
  DEFAULT: "Une erreur est survenue",
  INPUT_MISSING: "Veuillez entrer une description",
  SPOT_CREATE: "Erreur lors de la création du spot"
};

function NewSpotDetails() {
  const location = useLocation();
  const navigate = useNavigate();
  const id = useId();

  const { longitude, latitude } = location.state;
  const [description, setDescription] = useState("");
  const [errorMessage, setErrorMessage] = useState<string | null>(null);

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();

    if (!description.trim()) {
      setErrorMessage(ERROR_MESSAGES.INPUT_MISSING);
      return;
    }

    try {
      const requestBody = {longitude, latitude, description};
      const response = await createSpot(requestBody);
      
      console.log("Spot créé avec succès :", response);
      navigate("/");
    } catch (error) {
      if (error instanceof AxiosError) {
        setErrorMessage(ERROR_MESSAGES.SPOT_CREATE);
        console.error(error.response?.data?.error?.message);
      } else {
        setErrorMessage(ERROR_MESSAGES.DEFAULT);
        console.error(error);
      }
    }
  };

  return (
    <div className="relative flex flex-col items-center justify-start min-h-screen bg-light-grey w-full">
      <Header text={"Ajout d'un spot"} />

      {errorMessage && (
        <ErrorMessage
          errorMessage={errorMessage}
          setErrorMessage={setErrorMessage}
        />
      )}

      {/* Zone d'upload (futur uploader d'images) */}
      <div className="w-full px-4 py-2 bg-white">
        <div className="h-40 border-2 border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-sm rounded-lg mb-4">
          <p>Zone d'upload d'images (à implémenter)</p>
        </div>

        <div className="text-center mb-4">
          <MapPin size={36} className="text-red drop-shadow-lg mx-auto" />
          <p className="text-sm text-grey mt-2">
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
            />{" "}
          </div>

          <FormButton>Enregistrer</FormButton>
        </form>
      </div>
    </div>
  );
}

export default NewSpotDetails;
