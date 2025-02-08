import { Route, Routes } from "react-router";
import PrivateRoute from "./PrivateRoute";
import Home from "../../views/Home";
import Login from "../../views/Login";
import Register from "../../views/Register";
import AppLayout from "../../components/AppLayout";
import Community from "../../views/Community";
import Profile from "../../views/Profile";
import Settings from "../../views/Settings";
import NewSpotDetails from "../../views/newSpot/NewSpotDetails";
import Spot from "../../views/SpotDetails";
import NewSpotLocation from "../../views/newSpot/NewSpotLocation";

function Routing() {
  return (
    <Routes>
      {/* Public routes */}
      <Route path="/login" element={<Login />} />
      <Route path="/register" element={<Register />} />

      {/* Private routes with layout*/}
      <Route
        element={
          <PrivateRoute>
            <AppLayout />
          </PrivateRoute>
        }
      >
        <Route path="/" element={<Home />} />
        <Route path="/community" element={<Community />} />
        <Route path="/profile" element={<Profile />} />
        <Route path="/settings" element={<Settings />} />
        <Route path="/spot/add-location" element={<NewSpotLocation />} />
        <Route path="/spot/add-details" element={<NewSpotDetails />} />
        <Route path="/spot/:spotId" element={<Spot />} />
      </Route>

      {/* Route 404 */}
      <Route path="*" element={<Login />} />
    </Routes>
  );
}

export default Routing;
