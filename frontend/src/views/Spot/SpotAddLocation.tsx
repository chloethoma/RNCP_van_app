import { useRef, useEffect, useState } from "react";
import mapboxgl, { LngLatLike, Map } from "mapbox-gl";
import "mapbox-gl/dist/mapbox-gl.css";
import Button from "../../components/buttons/Button";
import { useLocation, useNavigate } from "react-router";
import { Pin } from "lucide-react";
import ErrorMessage from "../../components/ErrorMessage";

const DEFAULT_CENTER: LngLatLike = [2.20966, 46.2323];
const ZOOM: number = 18;
const MAPBOX_TOKEN: string = import.meta.env.VITE_MAPBOX_TOKEN;

const ERROR_MESSAGES = {
  DEFAULT: "Une erreur inconnue est survenue",
  CONTAINER: "La carte n'est pas encore prête.",
  MAP_INIT: "Erreur lors de l'initialisation de la carte.",
  GEOLOCATION_UNSUPPORTED:
    "La géolocalisation n'est pas supportée par votre navigateur.",
  GEOLOCATION_FAIL: "Impossible de récupérer votre position.",
  SPOTS_LOAD: "Erreur lors du chargement des spots.",
  SPOT_LOAD: "Erreur lors du chargement du spot.",
};

function SpotAddLocation() {
  const mapRef = useRef<Map | null>(null);
  const mapContainerRef = useRef<HTMLDivElement | null>(null);
  const navigate = useNavigate();
  const location = useLocation();

  const [errorMessage, setErrorMessage] = useState<string | null>(null);

  const handleValidateLocation = () => {
    if (!mapRef.current) {
      setErrorMessage(ERROR_MESSAGES.CONTAINER);
      return;
    }

    const center = mapRef.current.getCenter();
    if (!center) {
      setErrorMessage(ERROR_MESSAGES.GEOLOCATION_FAIL);
      return;
    }

    navigate("/spot/add-details", {
      state: { longitude: center.lng, latitude: center.lat },
    });
  };

  // Initialize the map with user location as center
  useEffect(() => {
    if (!mapContainerRef.current) {
      setErrorMessage(ERROR_MESSAGES.MAP_INIT);
      return;
    }

    const userLocation = location.state?.userLocation || DEFAULT_CENTER;

    mapboxgl.accessToken = MAPBOX_TOKEN;
    const map = new mapboxgl.Map({
      container: mapContainerRef.current,
      center: userLocation,
      zoom: ZOOM,
    });

    mapRef.current = map;

    return () => {
      map.remove();
    };
  }, [location.state]);

  return (
    <>
      <div ref={mapContainerRef} className="h-full w-full bg-light-grey" />

      <ErrorMessage
        errorMessage={errorMessage}
        setErrorMessage={setErrorMessage}
      />

      <div className="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-20">
        <Pin size={40} className="text-red drop-shadow-lg" />
      </div>
      <div className="flex fixed bottom-28 left-1/2 transform -translate-x-1/2 z-10 gap-4">
        <Button onClick={handleValidateLocation} label="C'est ici !"/>
        <Button onClick={() => navigate(-1)} color="red" label="Quitter"/>
      </div>
    </>
  );
}

export default SpotAddLocation;
