import { Route, Routes } from "react-router";
import PrivateRoute from "./PrivateRoute";
import Home from "../../views/Home";
import Login from "../../views/Login";
import Register from "../../views/Register";
import AppLayout from "../../components/AppLayout";
import Community from "../../views/Community";
import Profile from "../../views/Profile";
import Settings from "../../views/Settings";
import SpotAddDetails from "../../views/Spot/SpotAddDetails";
import Spot from "../../views/Spot/SpotDetails";
import SpotAddLocation from "../../views/Spot/SpotAddLocation";
import SpotEdit from "../../views/Spot/SpotEdit";
import SearchUser from "../../views/SearchUser";

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
        <Route path="/search/user" element={<SearchUser />} />
        <Route path="/profile" element={<Profile />} />
        <Route path="/settings" element={<Settings />} />
        <Route path="/spot/add-location" element={<SpotAddLocation />} />
        <Route path="/spot/add-details" element={<SpotAddDetails />} />
        <Route path="/spot/:spotId" element={<Spot />} />
        <Route path="/spot/:spotId/edit" element={<SpotEdit />} />
      </Route>

      {/* Route 404 */}
      <Route path="*" element={<Login />} />
    </Routes>
  );
}

export default Routing;
