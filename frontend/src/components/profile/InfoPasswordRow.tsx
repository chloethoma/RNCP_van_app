import { Pencil } from "lucide-react";
import IconButton from "../buttons/IconButton";

interface InfoPasswordRowProps {
  label: string;
  value: string;
  onEditPassword: () => void;
}

function InfoPasswordRow({
  label,
  value,
  onEditPassword,
}: InfoPasswordRowProps) {
  return (
    <div className="flex justify-between items-center py-2 border-b border-light-grey">
      <div className="flex-1">
        <p className="text-sm text-grey">{label}</p>
        <p className="text-sm font-semibold">{value}</p>
      </div>
      <IconButton
        onClick={onEditPassword}
        color="white"
        icon={<Pencil size={16} color="black" />}
      />
    </div>
  );
}

export default InfoPasswordRow;
