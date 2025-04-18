import { useLocation, useNavigate } from "react-router";
import { Spot } from "../../types/spot";
import Header from "../../components/headers/Header";
import ErrorMessage from "../../components/messages/ErrorMessage";
import { MapPin } from "lucide-react";
import FormButton from "../../components/buttons/FormButton";
import { useId, useState } from "react";
import { updateSpot } from "../../services/api/apiRequests";
import { messages } from "../../services/helpers/messagesHelper";

function SpotEdit() {
  const location = useLocation();
  const navigate = useNavigate();
  const id = useId();
  const { spot }: { spot: Spot } = location.state;

  const [errorMessage, setErrorMessage] = useState<string | null>(null);
  const [updatedSpot, setUpdatedSpot] = useState<Spot>({ ...spot });

  const handleChange = (e: React.ChangeEvent<HTMLTextAreaElement>) => {
    setUpdatedSpot({ ...updatedSpot, description: e.target.value });
  };

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();

    if (!updatedSpot.description.trim()) {
      setErrorMessage(messages.error_description_missing);
      return;
    }

    try {
      await updateSpot(updatedSpot);

      navigate(`/spots/${updatedSpot.id}`, {
        state: { spot: updatedSpot, successMessage: messages.success_spot_update },
      });
    } catch (error) {
        setErrorMessage(error instanceof Error ? error.message : messages.error_default);
    }
  };

  return (
    <>
      <div className="relative flex flex-col items-center justify-start min-h-screen bg-light-grey w-full">
        <Header text={"MODIFICATION DU SPOT"} />

        <ErrorMessage
          errorMessage={errorMessage}
          setErrorMessage={setErrorMessage}
        />

        <div className="w-full max-w-lg bg-white shadow-lg rounded-xl p-6 relative overflow-y-auto h-[calc(100vh-4rem-5rem)]">
          <div className="h-36 border-2 border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-sm rounded-lg mb-4">
            <p>Zone d'upload d'images (à implémenter)</p>
          </div>

          <div className="text-center mb-4">
            <MapPin size={26} className="text-red drop-shadow-lg mx-auto" />
            <p className="text-xs text-grey mt-2">
              Latitude: {updatedSpot.latitude} | Longitude:{" "}
              {updatedSpot.longitude}
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
                value={updatedSpot.description}
                onChange={handleChange}
              />
            </div>

            <FormButton>Enregistrer</FormButton>
          </form>
        </div>
      </div>
    </>
  );
}

export default SpotEdit;
