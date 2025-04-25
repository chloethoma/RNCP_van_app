import { Home, Users, User, Settings } from "lucide-react";
import NavItem from "./NavItem";

function BottomNavBar() {
  return (
    <nav className="fixed bottom-0 left-0 w-full bg-white flex rounded-t justify-around pt-3 pb-9 z-20 border-t-1 border-border-grey">
      <NavItem to={"/"} label={"Home"}>
        <Home size={24} />
      </NavItem>

      <NavItem to={"/friendships"} label={"Ma commu"}>
        <Users size={24} />
      </NavItem>

      <NavItem to={"/profile"} label={"Mon Profil"}>
        <User size={24} />
      </NavItem>

      <NavItem to={"/settings"} label={"Paramètres"}>
        <Settings size={24} />
      </NavItem>
    </nav>
  );
}

export default BottomNavBar;
