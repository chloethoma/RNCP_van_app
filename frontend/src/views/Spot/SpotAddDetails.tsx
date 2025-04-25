import { MapPin } from "lucide-react";
import { useLocation, useNavigate } from "react-router";
import { useId, useState } from "react";
import Header from "../../components/headers/Header";
import FormButton from "../../components/buttons/FormButton";
import ErrorMessage from "../../components/messages/ErrorMessage";
import { createSpot } from "../../services/api/apiRequests";
import { messages } from "../../services/helpers/messagesHelper";

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
      setErrorMessage(messages.error_description_missing);
      return;
    }

    try {
      const requestBody = { longitude, latitude, description };
      await createSpot(requestBody);

      navigate("/", {
        state: { successMessage: messages.success_spot_create },
      });
    } catch (error) {
      if (error instanceof Error) {
        setErrorMessage(error.message);
      } else {
        setErrorMessage(messages.error_default);
      }
    }
  };

  return (
    <>
      <Header text={"AJOUTER UN SPOT"} />
      <div className="flex flex-col items-center p-2 min-h-screen bg-light-grey font-default">
        <ErrorMessage
          errorMessage={errorMessage}
          setErrorMessage={setErrorMessage}
        />

        <div className="w-full max-w-lg bg-white shadow-lg rounded-xl p-6 relative h-[calc(100vh-4rem-5rem)]">
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
              <label
                htmlFor={id}
                className="text-sm font-medium text-dark-grey"
              >
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
    </>
  );
}

export default SpotAddDetails;
