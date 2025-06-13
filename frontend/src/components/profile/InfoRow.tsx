import { useEffect, useState } from "react";
import IconButton from "../buttons/IconButton";
import { Check, Pencil } from "lucide-react";

interface InfoRowProps {
  label: string;
  value: string;
  onSave: (value: string) => void;
  type?: string;
}

function InfoRow({ label, value, onSave }: InfoRowProps) {
  const [isEditing, setIsEditing] = useState<boolean>(false);
  const [inputValue, setInputValue] = useState<string>(value);

  useEffect(() => {
    if (!isEditing) {
      setInputValue(value);
    }
  }, [value, isEditing]);

  const handleSave = () => {
    onSave(inputValue);
    setIsEditing(false);
  };

  return (
    <div className="flex justify-between items-center py-2 border-b border-light-grey gap-4">
      <div className="flex-1">
        <p className="text-sm text-grey">{label}</p>
        {isEditing ? (
          <input
            type="text"
            value={inputValue}
            onChange={(e) => setInputValue(e.target.value)}
            className="text-sm font-semibold border border-gray-300 px-2 py-1 rounded-md w-full"
          />
        ) : (
          <p className="text-sm font-semibold">{value}</p>
        )}
      </div>
      {isEditing ? (
        <IconButton
          onClick={handleSave}
          color="white"
          icon={<Check size={16} color="green" strokeWidth={3} />}
        />
      ) : (
        <IconButton
          onClick={() => setIsEditing(true)}
          color="white"
          icon={<Pencil size={16} color="black" />}
        />
      )}
    </div>
  );
}

export default InfoRow;
