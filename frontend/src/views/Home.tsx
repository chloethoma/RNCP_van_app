import { useRef, useEffect, useState, useCallback } from "react";
import mapboxgl, { Map, LngLatLike } from "mapbox-gl";
import "mapbox-gl/dist/mapbox-gl.css";
import { Feature } from "../types/feature";
import { fetchSpotById, fetchSpots } from "../services/api/apiRequests";
import { Spot } from "../types/spot";
import SpotPreview from "../components/SpotPreview";
import { Locate, Pin, Plus } from "lucide-react";
import Button from "../components/buttons/Button";
import { useNavigate } from "react-router";
import { AxiosError } from "axios";

const INITIAL_CENTER: LngLatLike = [2.20966, 46.2323];
const INITIAL_ZOOM: number = 5;
const ZOOM: number = 10;
const SUPER_ZOOM: number = 18;
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

const ERROR_CONSOLE = {
  DEFAULT: "Unkown error",
  MAP_INIT: "Map init error",
  GEOLOCATION_FAIL: "Failed to geolocate",
  SPOTS_LOAD: "Failed to load spots",
  SPOT_LOAD: "Failed to load spot details",
};

function Home() {
  const mapRef = useRef<Map | null>(null);
  const mapContainerRef = useRef<HTMLDivElement | null>(null);
  const [selectedSpot, setSelectedSpot] = useState<Spot | null>(null);
  const [ErrorMessage, setErrorMessage] = useState<string | null>(null);
  const [addingSpot, setAddingSpot] = useState<boolean>(false);
  const navigate = useNavigate();

  const handleMarkerClick = async (spot: Feature) => {
    try {
      const spotDetails = await fetchSpotById(spot.properties.id);
      setSelectedSpot(spotDetails);
    } catch (error) {
      if (error instanceof AxiosError) {
        setErrorMessage(ERROR_MESSAGES.SPOT_LOAD);
        console.error(
          ERROR_CONSOLE.SPOT_LOAD,
          error.response?.data?.error?.message
        );
      } else {
        setErrorMessage(ERROR_MESSAGES.DEFAULT);
        console.error(ERROR_CONSOLE.DEFAULT, error);
      }
    }
  };

  const handleAddSpotStart = () => {
    getCurrentPositionAndFlyTo(SUPER_ZOOM);
    setAddingSpot(true);
  };

  const handleAddSpotValidate = () => {
    if (!mapRef.current) return;

    const center = mapRef.current.getCenter();

    setAddingSpot(false);
    navigate("/spot/add", {
      state: { lng: center.lng, lat: center.lat },
    });
  };

  const handleAddSpotExit = () => {
    setAddingSpot(false);
    mapRef.current!.flyTo({
      zoom: ZOOM,
    });
  };

  const handleGeolocalisation = useCallback(() => {
    getCurrentPositionAndFlyTo(ZOOM);
  }, []);

  const getCurrentPositionAndFlyTo = (zoom: number) => {
    if (!mapRef.current) {
      console.warn(ERROR_MESSAGES.CONTAINER);
      return;
    }

    if (!navigator.geolocation) {
      setErrorMessage(ERROR_MESSAGES.GEOLOCATION_UNSUPPORTED);
      return;
    }

    navigator.geolocation.getCurrentPosition(
      (position) => {
        const userCoords: LngLatLike = [
          position.coords.longitude,
          position.coords.latitude,
        ];
        mapRef.current!.flyTo({
          center: userCoords,
          zoom: zoom,
        });
      },
      (error) => {
        setErrorMessage(ERROR_MESSAGES.GEOLOCATION_FAIL);
        console.error(ERROR_CONSOLE.GEOLOCATION_FAIL, error);
      }
    );
  };

  // Initialize the map
  useEffect(() => {
    if (!mapContainerRef.current) {
      console.error(ERROR_MESSAGES.CONTAINER);
      return;
    }

    mapboxgl.accessToken = MAPBOX_TOKEN;
    const map = new mapboxgl.Map({
      container: mapContainerRef.current,
      center: INITIAL_CENTER,
      zoom: INITIAL_ZOOM,
    });

    mapRef.current = map;

    return () => {
      map.remove();
    };
  }, []);

  // Geolocalize and center to user location
  useEffect(() => {
    if (!mapRef.current) return;

    const map = mapRef.current;
    const onLoad = () => handleGeolocalisation();

    map.on("load", onLoad);

    return () => {
      map.off("load", onLoad);
    };
  }, [handleGeolocalisation]);

  // Fetch and display spots
  useEffect(() => {
    const loadSpots = async () => {
      try {
        const spotList = await fetchSpots();

        if (!mapRef.current || spotList.length === 0) return;

        spotList.forEach((spot) => {
          const marker = new mapboxgl.Marker()
            .setLngLat(spot.geometry.coordinates)
            .addTo(mapRef.current!);

          marker
            .getElement()
            .addEventListener("click", () => handleMarkerClick(spot));
        });
      } catch (error) {
        if (error instanceof AxiosError) {
          setErrorMessage(ERROR_MESSAGES.SPOTS_LOAD);
          console.error(
            ERROR_CONSOLE.SPOTS_LOAD,
            error.response?.data?.error?.message
          );
        } else {
          setErrorMessage(ERROR_MESSAGES.DEFAULT);
          console.error(ERROR_CONSOLE.DEFAULT, error);
        }
      }
    };

    loadSpots();
  }, []);

  return (
    <>
      <div ref={mapContainerRef} className="h-full w-full bg-light-grey" />

      {ErrorMessage && (
        <div className="fixed top-4 left-1/2 transform -translate-x-1/2 bg-red-500 text-white p-2 rounded">
          {ErrorMessage}
          <button
            onClick={() => setErrorMessage(null)}
            className="ml-2 font-bold"
          >
            X
          </button>
        </div>
      )}

      {selectedSpot && (
        <SpotPreview
          selectedSpot={selectedSpot}
          setSelectedSpot={setSelectedSpot}
        />
      )}

      {!addingSpot && !selectedSpot && (
        <>
          <div className="fixed bottom-26 right-4 flex flex-col items-end space-y-3 z-10">
            <Button onClick={handleAddSpotStart}>
              <Plus size={22} />
            </Button>
          </div>
          <div className="fixed bottom-40 right-4 flex flex-col items-end space-y-3 z-10">
            <Button onClick={handleGeolocalisation}>
              <Locate size={22} />
            </Button>
          </div>
        </>
      )}

      {addingSpot && (
        <>
          <div className="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-20">
            <Pin size={40} className="text-red drop-shadow-lg" />
          </div>
          <div className="flex fixed bottom-28 left-1/2 transform -translate-x-1/2 z-10 gap-4">
            <Button onClick={handleAddSpotValidate}>C'est ici !</Button>
            <Button onClick={handleAddSpotExit} color="red">
              Quitter
            </Button>
          </div>
        </>
      )}
    </>
  );
}

export default Home;
