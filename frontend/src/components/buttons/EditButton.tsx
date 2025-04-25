import IconButton from "./IconButton";
import { Pencil } from "lucide-react";

interface EditButtonProps {
  onClick: (event: React.MouseEvent<HTMLButtonElement>) => void;
}

function EditButton({ onClick }: EditButtonProps) {
  return <IconButton onClick={onClick} color="white" icon={<Pencil size={16} color="black" />} />;
}

export default EditButton;
