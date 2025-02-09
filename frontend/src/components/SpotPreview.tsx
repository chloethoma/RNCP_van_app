import { Spot } from "../types/spot";
import Button from "./buttons/Button";
import { X } from 'lucide-react';

interface SpotPreviewProps {
  selectedSpot: Spot;
  setSelectedSpot: (spot: Spot | null) => void;
}

function SpotPreview({ selectedSpot, setSelectedSpot }: SpotPreviewProps) {
  return (
    <>
      <div className="z-30 fixed bottom-24 w-full bg-white shadow-lg rounded-xl p-4 transition-transform duration-300 ease-in-out transform translate-y-0">
        <div className="relative">
          <div className="absolute top-1 right-1">
            <Button onClick={() => setSelectedSpot(null)} size="small">
              <X size={20}/>
            </Button>
          </div>
          <h3 className="text-lg text-dark-grey font-default font-semibold">
            {selectedSpot.id}
          </h3>
          <p className="text-dark-grey font-default text-sm">
            {selectedSpot.description}
          </p>
        </div>
      </div>
    </>
  );
}

export default SpotPreview;
