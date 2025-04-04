import React from "react";
import { Navigate } from "react-router";
import { jwtDecode } from "jwt-decode";

interface PrivateRouteProps {
  children: React.ReactNode;
}

function isTokenValid(): boolean {
  const token = localStorage.getItem("access_token");
  if (!token) return false;

  try {
    const decoded: { exp: number } = jwtDecode(token);
    const now = Date.now() / 1000;
    return decoded.exp > now;
  } catch (error) {
    console.log(error);
    return false;
  }
}

function PrivateRoute({ children }: PrivateRouteProps): JSX.Element {
  return isTokenValid() ? <>{children}</> : <Navigate to="/login" />;
}

export default PrivateRoute;