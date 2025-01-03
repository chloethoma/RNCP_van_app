import { useRef, useEffect, useState } from "react";
import mapboxgl from "mapbox-gl";

import "mapbox-gl/dist/mapbox-gl.css";

import "./UserMap.css";

const INITIAL_CENTER = [2.20966, 46.2323];
const INITIAL_ZOOM = 5.8;
const MAPBOX_TOKEN = import.meta.env.VITE_MAPBOX_TOKEN;

function UserMap({onLogOut}) {
  const mapRef = useRef();
  const mapContainerRef = useRef();

  const [center, setCenter] = useState(INITIAL_CENTER);
  const [zoom, setZoom] = useState(INITIAL_ZOOM);
  const [spots, setSpots] = useState([]);

  // Initialize the map
  useEffect(() => {
    mapboxgl.accessToken = MAPBOX_TOKEN;
    mapRef.current = new mapboxgl.Map({
      container: mapContainerRef.current,
      center: center,
      zoom: zoom,
    });

    mapRef.current.on("move", () => {
      // get the current center coordinates and zoom level from the map
      const mapCenter = mapRef.current.getCenter();
      const mapZoom = mapRef.current.getZoom();

      // update state
      setCenter([mapCenter.lng, mapCenter.lat]);
      setZoom(mapZoom);
    });

    return () => {
      mapRef.current.remove();
    };
  }, []);


  // Fetch spots from API
  useEffect(() => {
    const fetchSpots = async () => {
      try {
        const response = await fetch("https://localhost/spots");
        const data = await response.json();
        setSpots(data.features);
      } catch (error) {
        console.error("Failed to fetch spots:", error);
      }
    };

    fetchSpots();
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

  const handleLogOut = () => {
    onLogOut(false);
  }

  return (
    <>
      <div className="sidebar">
        Longitude: {center[0].toFixed(4)} | Latitude: {center[1].toFixed(4)} |
        Zoom: {zoom.toFixed(2)}
      </div>

      <button className="reset-button" onClick={handleButtonClick}>
        Reset
      </button>

      <button className="deconnection-button" onClick={handleLogOut}>
        Déconnexion
      </button>

      <div id="map-container" ref={mapContainerRef} />
    </>
  );
}

export default UserMap;
