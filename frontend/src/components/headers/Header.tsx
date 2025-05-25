import { useNavigate } from "react-router";
import IconButton from "../buttons/IconButton";
import { ArrowLeft } from "lucide-react";

interface HeaderProps {
  text: string;
}

function Header({ text }: HeaderProps) {
  const navigate = useNavigate();

  return (
    <div className="fixed top-0 left-0 w-full h-14 bg-white flex items-center z-50 shadow-md">
      <div className="absolute left-4">
        <IconButton onClick={() => navigate(-1)} size={"small"} icon={<ArrowLeft size={20} />}/>
      </div>
      <h1 className="mx-auto text-xl font-semibold text-dark-grey">{text}</h1>
    </div>
  );
}

export default Header;
