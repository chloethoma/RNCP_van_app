import { NavLink } from "react-router";

interface NavItemProps {
  to: string;
  label: string;
  children: JSX.Element;
}

function NavItem({ to, label, children }: NavItemProps) {
  return (
    <NavLink
      to={to}
      className="flex flex-col items-center text-grey hover:text-grey-hover"
    >
      {children}
      <span className="text-xs test-black">{label}</span>
    </NavLink>
  );
}

export default NavItem;
