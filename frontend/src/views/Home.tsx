import "mapbox-gl/dist/mapbox-gl.css";
import { useRef, useEffect, useState, useCallback, useContext } from "react";
import mapboxgl, { Map, LngLatLike } from "mapbox-gl";
import {
  fetchSpoFriendsList,
  fetchSpotById,
  fetchSpotFriendsById,
  fetchSpotList,
  fetchUserByToken,
} from "../services/api/apiRequests";
import { Spot, SpotGeoJson } from "../types/spot";
import SpotPreview from "../components/SpotPreview";
import { Locate, Plus } from "lucide-react";
import IconButton from "../components/buttons/IconButton";
import { useLocation, useNavigate } from "react-router";
import ErrorMessage from "../components/messages/ErrorMessage";
import SuccessMessage from "../components/messages/SuccessMessage";
import Toggle from "../components/toggle/Toggle";
import UserContext from "../hooks/UserContext";
import { messages } from "../services/helpers/messagesHelper";

const DEFAULT_CENTER: LngLatLike = [2.20966, 46.2323];
const DEFAULT_ZOOM: number = 4.5;
const ZOOM: number = 10;
const MAPBOX_TOKEN: string = import.meta.env.VITE_MAPBOX_TOKEN;

function Home() {
  const mapRef = useRef<Map | null>(null);
  const mapContainerRef = useRef<HTMLDivElement | null>(null);
  const navigate = useNavigate();
  const location = useLocation();
  const userContext = useContext(UserContext);
  const { setUser } = userContext || {};

  const [errorMessage, setErrorMessage] = useState<string | null>(null);
  const [successMessage, setSuccessMessage] = useState<string | null>(
    location.state?.successMessage || null
  );
  const [userLocation, setUserLocation] = useState<LngLatLike>();
  const [selectedSpot, setSelectedSpot] = useState<Spot | null>();
  const [viewOwnSpots, setViewOwnSpots] = useState<boolean>(true);
  const markersRef = useRef<mapboxgl.Marker[]>([]);

  // Initialize the map
  useEffect(() => {
    if (!mapContainerRef.current) {
      console.error(messages.error_container);
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

  // Fetch the user data and set in UserContext
  useEffect(() => {
    const fetchUser = async () => {
      try {
        const userData = await fetchUserByToken();

        if (setUser) {
          setUser(userData);
        }
      } catch (error) {
        console.error(
          "Erreur lors de la récupération de l'utilisateur :",
          error
        );
      }
    };

    fetchUser();
  }, [setUser]);

  // Ask user to get his location with first rendering
  useEffect(() => {
    if (!navigator.geolocation) {
      setErrorMessage(messages.error_geolocation_unsupported);
      return;
    }

    navigator.geolocation.getCurrentPosition(
      (position) => {
        setUserLocation([position.coords.longitude, position.coords.latitude]);
      },
      (error) => {
        setErrorMessage(messages.error_geolocation_fail);
        console.error(error);
      }
    );
  }, []);

  const handleMarkerClick = useCallback(
    async (spot: SpotGeoJson) => {
      try {
        let fetchedSpot: Spot;

        if (viewOwnSpots) {
          fetchedSpot = await fetchSpotById(spot.properties.spotId);
        } else {
          fetchedSpot = await fetchSpotFriendsById(spot.properties.spotId);
        }

        setSelectedSpot(fetchedSpot);
      } catch (error) {
        setErrorMessage(error instanceof Error ? error.message : messages.error_default);
      }
    },
    [viewOwnSpots]
  );

  // Fetch and display spots
  useEffect(() => {
    const loadSpots = async () => {
      if (!mapRef.current) return;

      try {
        let spotList;

        if (viewOwnSpots) {
          spotList = await fetchSpotList();
        } else {
          spotList = await fetchSpoFriendsList();
        }

        markersRef.current.forEach((marker) => marker.remove());
        markersRef.current = [];

        if (spotList.length === 0) return;

        spotList.forEach((spot) => {
          const marker = new mapboxgl.Marker()
            .setLngLat(spot.geometry.coordinates)
            .addTo(mapRef.current!);

          marker
            .getElement()
            .addEventListener("click", () => handleMarkerClick(spot));

          markersRef.current.push(marker);
        });
      } catch (error) {
        setErrorMessage(
          error instanceof Error ? error.message : messages.error_default
        );
      }
    };

    loadSpots();
  }, [viewOwnSpots, handleMarkerClick]);

  const getCurrentPositionAndFlyTo = (zoom: number) => {
    if (!mapRef.current) {
      console.warn(messages.error_container);
      return;
    }

    if (!userLocation) {
      setErrorMessage(messages.error_geolocation_fail);
      return;
    }

    mapRef.current.flyTo({
      center: userLocation,
      zoom: zoom,
    });
  };

  const handleNavigate = () => {
    if (userLocation) {
      navigate("/spots/add-location", { state: { userLocation } });
    } else {
      setErrorMessage(messages.error_geolocation_fail);
    }
  };

  return (
    <>
      <div ref={mapContainerRef} className="h-full w-full bg-light-grey" />

      <div className="fixed top-6 w-full flex justify-center z-50">
        <ErrorMessage
          errorMessage={errorMessage}
          setErrorMessage={setErrorMessage}
        />
        <SuccessMessage
          successMessage={successMessage}
          setSuccessMessage={setSuccessMessage}
        />
      </div>

      <div className="fixed top-6 w-full flex justify-center">
        <Toggle
          options={[
            { label: "Mes spots", defaultValue: true },
            { label: "Spots de la commu", defaultValue: false },
          ]}
          selectedValue={viewOwnSpots}
          onChange={setViewOwnSpots}
        />
      </div>

      {selectedSpot && (
        <SpotPreview
          selectedSpot={selectedSpot}
          setSelectedSpot={setSelectedSpot}
        />
      )}

      {!selectedSpot && (
        <>
          <div className="fixed bottom-26 right-4 flex flex-col items-end space-y-3 z-10">
            <IconButton onClick={handleNavigate} icon={<Plus size={22} />} />
          </div>
          <div className="fixed bottom-40 right-4 flex flex-col items-end space-y-3 z-10">
            <IconButton
              onClick={() => getCurrentPositionAndFlyTo(ZOOM)}
              icon={<Locate size={22} />}
            />
          </div>
        </>
      )}
    </>
  );
}

export default Home;
