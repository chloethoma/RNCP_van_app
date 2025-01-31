import { Route, Routes } from "react-router";
import PrivateRoute from "./PrivateRoute";
import Home from "../../views/Home";
import Login from "../../views/Login";
import Register from "../../views/Register";
import AppLayout from "../../components/AppLayout";
import Community from "../../views/Community";
import Profile from "../../views/Profile";
import Settings from "../../views/Settings";

function Routing() {
  return (
    <Routes>
      <Route path="/login" element={<Login />} />
      <Route path="/register" element={<Register />} />

      <Route element={<AppLayout />}>
        <Route
          path="/"
          element={
            <PrivateRoute>
              <Home />
            </PrivateRoute>
          }
        />
        <Route
          path="/community"
          element={
            <PrivateRoute>
              <Community />
            </PrivateRoute>
          }
        />
        <Route
          path="/profile"
          element={
            <PrivateRoute>
              <Profile />
            </PrivateRoute>
          }
        />
        <Route
          path="/settings"
          element={
            <PrivateRoute>
              <Settings />
            </PrivateRoute>
          }
        />
      </Route>
      <Route path="*" element={<Login />} />
    </Routes>
  );
}

export default Routing;
