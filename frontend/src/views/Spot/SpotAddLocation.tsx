import { useRef, useEffect, useState } from "react";
import mapboxgl, { LngLatLike, Map } from "mapbox-gl";
import "mapbox-gl/dist/mapbox-gl.css";
import IconButton from "../../components/buttons/IconButton";
import { useLocation, useNavigate } from "react-router";
import { Pin } from "lucide-react";
import ErrorMessage from "../../components/messages/ErrorMessage";
import { messages } from "../../services/helpers/messagesHelper";

const DEFAULT_CENTER: LngLatLike = [2.20966, 46.2323];
const ZOOM: number = 18;
const MAPBOX_TOKEN: string = import.meta.env.VITE_MAPBOX_TOKEN;

function SpotAddLocation() {
  const mapRef = useRef<Map | null>(null);
  const mapContainerRef = useRef<HTMLDivElement | null>(null);
  const navigate = useNavigate();
  const location = useLocation();

  const [errorMessage, setErrorMessage] = useState<string | null>(null);

  const handleValidateLocation = () => {
    if (!mapRef.current) {
      setErrorMessage(messages.error_container);
      return;
    }

    const center = mapRef.current.getCenter();
    if (!center) {
      setErrorMessage(messages.error_geolocation_fail);
      return;
    }

    navigate("/spots/add-details", {
      state: { longitude: center.lng, latitude: center.lat },
    });
  };

  // Initialize the map with user location as center
  useEffect(() => {
    if (!mapContainerRef.current) {
      setErrorMessage(messages.error_map_init);
      return;
    }

    const userLocation = location.state?.location || DEFAULT_CENTER;
    const zoom = location.state?.zoom || ZOOM;

    mapboxgl.accessToken = MAPBOX_TOKEN;
    const map = new mapboxgl.Map({
      container: mapContainerRef.current,
      center: userLocation,
      zoom: zoom,
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
        <IconButton onClick={handleValidateLocation} label="C'est ici !" />
        <IconButton onClick={() => navigate(-1)} color="red" label="Quitter" />
      </div>
    </>
  );
}

export default SpotAddLocation;
