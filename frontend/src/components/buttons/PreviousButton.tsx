import IconButton from "./IconButton";
import { ArrowLeft } from "lucide-react";

interface PreviousButtonProps {
  onClick: (event: React.MouseEvent<HTMLButtonElement>) => void;
}

function PreviousButton({ onClick }: PreviousButtonProps) {
  return (
    <IconButton
      onClick={onClick}
      size={"small"}
      icon={<ArrowLeft size={20} />}
    />
  );
}

export default PreviousButton;
