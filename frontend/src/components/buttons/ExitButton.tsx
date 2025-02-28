import IconButton from "./IconButton";
import { X } from "lucide-react";

interface ExitButtonProps {
  onClick: (event: React.MouseEvent<HTMLButtonElement>) => void;
}

function ExitButton({ onClick }: ExitButtonProps) {
  return <IconButton onClick={onClick} size={"small"} icon={<X size={20} />} />;
}

export default ExitButton;
