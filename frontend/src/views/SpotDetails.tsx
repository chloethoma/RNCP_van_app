import { useLocation, useNavigate } from "react-router";
import { Spot } from "../types/spot";
import PreviousButton from "../components/buttons/PreviousButton";

function SpotDetails() {
    const navigate = useNavigate();
    const location = useLocation();
    const spot: Spot = location.state.spot;

    return (
      <div className="flex flex-col items-center justify-center min-h-screen bg-light-grey text-grey p-2">
        
        <div className="absolute top-4 left-4">
          <PreviousButton onClick={() => navigate(-1)}/>
        </div>
  
        {spot ? (
          <div className="bg-white shadow-lg rounded-xl p-6 w-full max-w-lg">
            <h1 className="text-2xl font-bold text-dark-grey">Spot #{spot.id}</h1>
            <p className="text-grey test-xs mt-2">{spot.description}</p>
          </div>
        ) : (
          <h1 className="text-red">Spot introuvable !</h1>
        )}
      </div>
    );
}

export default SpotDetails;
