import { X } from "lucide-react";
import { useEffect } from "react";

interface SuccessMessageProps {
  successMessage: string | null;
  setSuccessMessage: React.Dispatch<React.SetStateAction<string | null>>;
}

function SuccessMessage({
  successMessage,
  setSuccessMessage,
}: SuccessMessageProps) {
  useEffect(() => {
    if (successMessage) {
      const timer = setTimeout(() => {
        setSuccessMessage(null);
      }, 3000);

      return () => clearTimeout(timer);
    }
  }, [successMessage, setSuccessMessage]);

  if (!successMessage) return null;

  return (
    <div className="fixed top-4 w-full flex justify-center items-center bg-green-500 text-white px-4 py-2 gap-3 rounded-xl shadow-lg max-w-[80vw] z-50">
      <p>{successMessage}</p>
      <button onClick={() => setSuccessMessage(null)}>
        <X size={20} />
      </button>
    </div>
  );
}

export default SuccessMessage;
