import { useRef, useEffect, useState } from "react";
import mapboxgl, { Map, LngLatLike } from "mapbox-gl";
import "mapbox-gl/dist/mapbox-gl.css";
import "./Home.css";
import { Feature } from "../../types/feature";
import {fetchSpots} from "../../services/api/apiRequests";

const INITIAL_CENTER: LngLatLike = [2.20966, 46.2323];
const INITIAL_ZOOM: number = 5.8;
const MAPBOX_TOKEN: string = import.meta.env.VITE_MAPBOX_TOKEN;

function Home() {
  const mapRef = useRef<Map>();
  const mapContainerRef = useRef<HTMLElement | string>();

  const [center, setCenter] = useState<LngLatLike>(INITIAL_CENTER);
  const [zoom, setZoom] = useState<number>(INITIAL_ZOOM);
  const [spots, setSpots] = useState<Feature[]>([]);

  // Initialize the map
  useEffect(() => {
    if (!mapContainerRef.current) {
      console.error("Le conteneur de la carte n'est pas défini.");
      return;
    }

    mapboxgl.accessToken = MAPBOX_TOKEN;
    mapRef.current = new mapboxgl.Map({
      container: mapContainerRef.current,
      center: INITIAL_CENTER,
      zoom: INITIAL_ZOOM,
    });

    mapRef.current.on("move", () => {
      if (!mapRef.current) return;
      // get the current center coordinates and zoom level from the map
      const mapCenter = mapRef.current.getCenter();
      const mapZoom = mapRef.current.getZoom();

      // update state
      setCenter([mapCenter.lng, mapCenter.lat]);
      setZoom(mapZoom);
    });

    return () => {
      if (mapRef.current) {
        mapRef.current.remove();
      }
    };
  }, []);

  // Fetch spots from API
  useEffect(() => {
    const loadSpots = async () => {
      try {
        const spots = await fetchSpots();
        setSpots(spots);
      } catch (error) {
        console.error("Failed to load spots:", error);
      }
    };

    loadSpots();
  }, []);

  // Display the spots on map
  useEffect(() => {
    if (mapRef.current && spots.length > 0) {
      spots.forEach((spot) => {
        // Create a marker for each spot
        new mapboxgl.Marker()
          .setLngLat(spot.geometry.coordinates)
          .addTo(mapRef.current);
      });
    }
  }, [spots]);

  const handleButtonClick = () => {
    mapRef.current.flyTo({
      center: INITIAL_CENTER,
      zoom: INITIAL_ZOOM,
    });
  };

  // const handleLogOut = () => {
  //   onLogOut(false);
  // }

  return (
    <>
      <div className="sidebar">
        Longitude: {center[0].toFixed(4)} | Latitude: {center[1].toFixed(4)} |
        Zoom: {zoom.toFixed(2)}
      </div>

      <button className="reset-button" onClick={handleButtonClick}>
        Reset
      </button>

      <div id="map-container" ref={mapContainerRef} />
    </>
  );
}

export default Home;
