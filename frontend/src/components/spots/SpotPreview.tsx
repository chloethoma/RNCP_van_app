import { Spot } from "../../types/spot";
import { useNavigate } from "react-router";
import Picture from "../../assets/picture_default.jpg";
import IconButton from "./../buttons/IconButton";
import { Eye, X } from "lucide-react";

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
    <div
      className="relative space-y-2 lg:space-y-5 cursor-pointer"
      onClick={handleNavigate}
    >
      <div className="relative w-full rounded-xl">
        <img
          src={selectedSpot.picture || Picture}
          alt="picture"
          className="w-full h-20 rounded-xl object-cover md:h-30"
        />
        <div className="absolute top-2 right-2 flex gap-2">
          <IconButton
            onClick={handleNavigate}
            size="small"
            color="white"
            icon={<Eye size={26} strokeWidth={2} color="#000000" />}
          />
          <IconButton
            onClick={(e) => {
              e.stopPropagation();
              setSelectedSpot(null);
            }}
            size="small"
            color="white"
            icon={<X size={26} strokeWidth={2} color="#000000" />}
          />
        </div>
      </div>

      <div className="m-3 flex flex-col gap-2">
        <h3 className="text-sm text-dark-grey font-default font-semibold line-clamp-1 md:line-clamp-none">
          Description
        </h3>
        <p className="text-dark-grey font-default text-xs line-clamp-2 md:line-clamp-none">
          {selectedSpot.description}
        </p>
      </div>
    </div>
  );
}

export default SpotPreview;
