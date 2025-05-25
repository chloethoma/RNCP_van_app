import { useLocation, useNavigate } from "react-router";
import { Spot } from "../../types/spot";
import ErrorMessage from "../../components/messages/ErrorMessage";
import { MapPin } from "lucide-react";
import FormSubmitButton from "../../components/buttons/FormSubmitButton";
import { useId, useState } from "react";
import { updateSpot } from "../../services/api/apiRequests";
import { messages } from "../../services/helpers/messagesHelper";
import ViewWithHeader from "../../components/headers/ViewWithHeader";

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
        state: {
          spot: updatedSpot,
          successMessage: messages.success_spot_update,
        },
      });
    } catch (error) {
      setErrorMessage(
        error instanceof Error ? error.message : messages.error_default,
      );
    }
  };

  return (
      <ViewWithHeader text={"MODIFICATION DU SPOT"}>
      <div className="flex flex-col items-center p-2 min-h-screen bg-light-grey font-default">
        <ErrorMessage
          errorMessage={errorMessage}
          setErrorMessage={setErrorMessage}
        />

        <div className="w-full max-w-lg bg-white shadow-lg rounded-xl p-6 relative">
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
            <div className="mt-3 pt-2">
              <label
                htmlFor={id}
                className="text-md font-medium text-dark-grey"
              >
                Description
              </label>
              <textarea
                id={id}
                className="w-full h-40 md:h-32 p-2 border text-sm border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-dark-green placeholder-top placeholder-left resize-none"
                placeholder="Ajoutez une description..."
                value={updatedSpot.description}
                onChange={handleChange}
              />
            </div>

            <FormSubmitButton>Enregistrer</FormSubmitButton>
          </form>
        </div>
      </div>
      </ViewWithHeader>
  );
}

export default SpotEdit;
