import { Outlet } from "react-router";
import BottomNavBar from "./bottomNavBar/BottomNavBar";

function AppLayout() {
  return (
    <div className="absolute inset-0">
      <Outlet />
      <BottomNavBar />
    </div>
  );
}

export default AppLayout;
