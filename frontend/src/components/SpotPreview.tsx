import { Spot } from "../types/spot";
import { useNavigate } from "react-router";
import ExitButton from "./buttons/ExitButton";

interface SpotPreviewProps {
  selectedSpot: Spot;
  setSelectedSpot: (spot: Spot | null) => void;
}

function SpotPreview({ selectedSpot, setSelectedSpot }: SpotPreviewProps) {
  const navigate = useNavigate();

  const handleNavigate = () => {
    navigate(`/spots/${selectedSpot.id}`, { state: { spot: selectedSpot } });
  };

  return (
    <>
      <div
        onClick={handleNavigate}
        className="fixed bottom-24 px-4 py-4 mx-4 sm:mx-6 md:mx-10 lg:mx-16 max-w-screen-md left-0 right-0 bg-white shadow-lg rounded-xl transition-transform duration-300 ease-in-out transform"
      >
        <div className="relative">
          <div className="absolute -top-1 -right-0.5">
            <ExitButton
              onClick={(e) => {
                e.stopPropagation();
                setSelectedSpot(null);
              }}
            ></ExitButton>
          </div>
          <h3 className="text-lg text-dark-grey font-default font-semibold line-clamp-1">
            {selectedSpot.id}
          </h3>
          <p className="text-dark-grey font-default text-sm line-clamp-2">
            {selectedSpot.description}
          </p>
        </div>
      </div>
    </>
  );
}

export default SpotPreview;
