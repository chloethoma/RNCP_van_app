import { useRoutes } from "react-router";
import PrivateRoute from "./PrivateRoute";
import Home from "../../views/Home/Home";
import Login from "../../views/Login/Login";
import Register from "../../views/Register/Register";

function Routes() {
  return useRoutes([
    {
      path: "/login",
      element: <Login />,
    },
    {
      path: "/register",
      element: <Register />,
    },
    {
      path: "/",
      element: (
        <PrivateRoute>
          <Home />
        </PrivateRoute>
      ),
    },
    {
      path: "*",
      element: <Login />,
    },
  ]);
}

export default Routes;
