import { Route, Routes } from "react-router";
import PrivateRoute from "./PrivateRoute";
import Home from "../../views/Home";
import Login from "../../views/Login";
import Register from "../../views/Register";
import AppLayout from "../../components/AppLayout";
import Profile from "../../views/Profile";
import Settings from "../../views/Settings";
import SpotAddDetails from "../../views/Spot/SpotAddDetails";
import Spot from "../../views/Spot/SpotDetails";
import SpotAddLocation from "../../views/Spot/SpotAddLocation";
import SpotEdit from "../../views/Spot/SpotEdit";
import SearchUser from "../../views/Friendship/SearchUser";
import PendingFriendships from "../../views/Friendship/PendingFriendships";
import Friendships from "../../views/Friendships";

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
        <Route path="/spots/add-location" element={<SpotAddLocation />} />
        <Route path="/spots/add-details" element={<SpotAddDetails />} />
        <Route path="/spots/:spotId" element={<Spot />} />
        <Route path="/spots/:spotId/edit" element={<SpotEdit />} />
        <Route path="/friendships" element={<Friendships />} />
        <Route path="/search/users" element={<SearchUser />} />
        <Route path="/friendships/pending" element={<PendingFriendships />} />
        <Route path="/profile" element={<Profile />} />
        <Route path="/settings" element={<Settings />} />
      </Route>

      {/* Route 404 */}
      <Route path="*" element={<Login />} />
    </Routes>
  );
}

export default Routing;
