import { useRef, useEffect, useState } from "react";
import mapboxgl, { Map, LngLatLike } from "mapbox-gl";
import "mapbox-gl/dist/mapbox-gl.css";
import { Feature } from "../types/feature";
import { fetchSpotById, fetchSpots } from "../services/api/apiRequests";
import { Spot } from "../types/spot";
import SpotPreview from "../components/SpotPreview";
import { Locate, Plus } from "lucide-react";
import Button from "../components/buttons/Button";
import { useNavigate } from "react-router";
import { AxiosError } from "axios";
import ErrorMessage from "../components/ErrorMessage";

const DEFAULT_CENTER: LngLatLike = [2.20966, 46.2323];
const DEFAULT_ZOOM: number = 4.5;
const ZOOM: number = 10;
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
  const navigate = useNavigate();

  const [userLocation, setUserLocation] = useState<LngLatLike | null>(null);
  const [selectedSpot, setSelectedSpot] = useState<Spot | null>(null);
  const [errorMessage, setErrorMessage] = useState<string | null>(null);

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

  const getCurrentPositionAndFlyTo = (zoom: number) => {
    if (!mapRef.current) {
      console.warn(ERROR_MESSAGES.CONTAINER);
      return;
    }

    if (!userLocation) {
      setErrorMessage(ERROR_MESSAGES.GEOLOCATION_FAIL);
      return;
    }

    mapRef.current.flyTo({
      center: userLocation,
      zoom: zoom,
    });
  };

  const handleNavigate = () => {
    if (userLocation) {
      navigate("/spot/add-location", { state: { userLocation } });
    } else {
      setErrorMessage(ERROR_MESSAGES.GEOLOCATION_FAIL);
    }
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
      center: DEFAULT_CENTER,
      zoom: DEFAULT_ZOOM,
    });

    mapRef.current = map;

    return () => {
      map.remove();
    };
  }, []);

  // Ask user to get his location with first rendering
  useEffect(() => {
    if (!navigator.geolocation) {
      setErrorMessage(ERROR_MESSAGES.GEOLOCATION_UNSUPPORTED);
      return;
    }

    navigator.geolocation.getCurrentPosition(
      (position) => {
        setUserLocation([position.coords.longitude, position.coords.latitude]);
      },
      (error) => {
        setErrorMessage(ERROR_MESSAGES.GEOLOCATION_FAIL);
        console.error(ERROR_CONSOLE.GEOLOCATION_FAIL, error);
      }
    );
  }, []);

  // Fetch and display spots
  useEffect(() => {
    const loadSpots = async () => {
      if (!mapRef.current) return;

      try {
        const spotList = await fetchSpots();

        if (spotList.length === 0) return;

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

      {errorMessage && (
        <ErrorMessage
          errorMessage={errorMessage}
          setErrorMessage={setErrorMessage}
        />
      )}

      {selectedSpot && (
        <SpotPreview
          selectedSpot={selectedSpot}
          setSelectedSpot={setSelectedSpot}
        />
      )}

      {!selectedSpot && (
        <>
          <div className="fixed bottom-26 right-4 flex flex-col items-end space-y-3 z-10">
            <Button onClick={handleNavigate}>
              <Plus size={22} />
            </Button>
          </div>
          <div className="fixed bottom-40 right-4 flex flex-col items-end space-y-3 z-10">
            <Button onClick={() => getCurrentPositionAndFlyTo(ZOOM)}>
              <Locate size={22} />
            </Button>
          </div>
        </>
      )}
    </>
  );
}

export default Home;
