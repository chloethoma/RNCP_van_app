import IconButton from "./IconButton";
import { Check } from "lucide-react";

interface SaveButtonProps {
  onClick: (event: React.MouseEvent<HTMLButtonElement>) => void;
}

function SaveButton({ onClick }: SaveButtonProps) {
  return (
    <IconButton
      onClick={onClick}
      color="white"
      icon={<Check size={16} color="green" strokeWidth={3} />}
    />
  );
}

export default SaveButton;
