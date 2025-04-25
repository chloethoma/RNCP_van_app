import { useNavigate } from "react-router";
import PreviousButton from "../buttons/PreviousButton";

interface HeaderProps {
  text: string;
}

function Header({ text }: HeaderProps) {
  const navigate = useNavigate();

  return (
    <div className="relative h-14 bg-white flex items-center">
      <div className="absolute left-4">
        <PreviousButton onClick={() => navigate(-1)} />
      </div>
      <h1 className="mx-auto text-xl font-semibold text-dark-grey">{text}</h1>
    </div>
  );
  }

export default Header;
