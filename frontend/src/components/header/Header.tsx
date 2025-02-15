import { useNavigate } from "react-router";
import PreviousButton from "../buttons/PreviousButton";

interface HeaderProps {
    text: string
}

function Header({text}: HeaderProps) {
  const navigate = useNavigate();

  return (
    <div className="w-full flex items-center mb-2 gap-6 bg-white h-14 p-4">
        <PreviousButton onClick={() => navigate(-1)}/>
      <h1 className="text-xl font-bold text-dark-grey">{text}</h1>
    </div>
  );
}

export default Header;
