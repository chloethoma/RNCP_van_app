import "mapbox-gl/dist/mapbox-gl.css";
import {
  useRef,
  useEffect,
  useState,
  useCallback,
  useContext,
  Fragment,
} from "react";
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
import {
  Popover,
  PopoverButton,
  PopoverPanel,
  Transition,
} from "@headlessui/react";
import ListButton from "../components/buttons/ListButton";

const DEFAULT_CENTER: LngLatLike = [2.20966, 46.2323];
const DEFAULT_ZOOM: number = 4.5;
const SUPER_ZOOM: number = 10;
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
    location.state?.successMessage || null,
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
          error,
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
      },
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
        setErrorMessage(
          error instanceof Error ? error.message : messages.error_default,
        );
      }
    },
    [viewOwnSpots],
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
          error instanceof Error ? error.message : messages.error_default,
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

  const handleNavigate = (location: LngLatLike, zoom: number) => {
    navigate("/spots/add-location", { state: { location, zoom } });
  };

  return (
    <>
      <div ref={mapContainerRef} className="h-full w-full" />

      {/* Toggle */}
      <div className="h-full w-full relative">
        <div className="fixed top-6 w-full flex justify-center z-10">
          <Toggle
            options={[
              { label: "Mes spots", defaultValue: true },
              { label: "Spots de la commu", defaultValue: false },
            ]}
            selectedValue={viewOwnSpots}
            onChange={setViewOwnSpots}
          />
        </div>

        {/* Icon geolocation and add location */}
        <div className="fixed bottom-26 right-4 flex flex-col space-y-3 z-10">
          <Popover className="relative">
            <PopoverButton>
              <IconButton icon={<Plus size={22} />} />
            </PopoverButton>
            <Transition
              as={Fragment}
              enter="transition ease-out duration-200"
              enterFrom="opacity-0 translate-y-1"
              enterTo="opacity-100 translate-y-0"
              leave="transition ease-in duration-150"
              leaveFrom="opacity-100 translate-y-0"
              leaveTo="opacity-0 translate-y-1"
            >
              <PopoverPanel
                anchor="top"
                className="flex flex-col space-y-2 [--anchor-gap:8px] ml-[-0.5rem]"
              >
                <ListButton
                  onClick={() => {
                    if (userLocation) {
                      handleNavigate(userLocation, SUPER_ZOOM);
                    }
                  }}
                  label="Sur ma position"
                  color="darkGreen"
                  disabled={!userLocation}
                />
                <ListButton
                  onClick={() => handleNavigate(DEFAULT_CENTER, DEFAULT_ZOOM)}
                  label="Ailleurs"
                  color="darkGreen"
                />
              </PopoverPanel>
            </Transition>
          </Popover>
          <IconButton
            onClick={() => getCurrentPositionAndFlyTo(SUPER_ZOOM)}
            icon={<Locate size={22} />}
            disabled={!userLocation}
          />
        </div>

        {/* Messages */}
        <ErrorMessage
          errorMessage={errorMessage}
          setErrorMessage={setErrorMessage}
        />
        <SuccessMessage
          successMessage={successMessage}
          setSuccessMessage={setSuccessMessage}
        />
      </div>

      {/* Spot Preview */}
      {selectedSpot && (
        <div
          className="fixed bottom-26 mx-4 left-0 right-0 
           bg-white shadow-lg rounded-xl z-10 transition-transform duration-300 ease-in-out transform 
           md:w-1/3"
        >
          <SpotPreview
            selectedSpot={selectedSpot}
            setSelectedSpot={setSelectedSpot}
          />
        </div>
      )}
    </>
  );
}

export default Home;
