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
      }, 4000);

      return () => clearTimeout(timer);
    }
  }, [successMessage, setSuccessMessage]);

  if (!successMessage) return null;

  return (
    <div className="fixed top-4 w-full flex justify-center items-center bg-success-bg border border-success-border px-4 py-2 gap-1 rounded-xl shadow-lg max-w-[80vw] z-50">
      <p className="text-success-text font-bold">{successMessage}</p>
      <button onClick={() => setSuccessMessage(null)}>
        <X size={20} color="#2e7d32"/>
      </button>
    </div>
  );
}

export default SuccessMessage;
