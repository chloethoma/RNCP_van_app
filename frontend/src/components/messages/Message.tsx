import { X } from "lucide-react";
import { useEffect } from "react";

interface MessageProps {
  message: string | null;
  setMessage: (value: string | null) => void;
  borderColor: string;
  bgColor: string;
  textColor: string;
  buttonColor: string;
}

function ErrorMessage({
  message,
  setMessage,
  borderColor,
  bgColor,
  textColor,
  buttonColor,
}: MessageProps) {
  useEffect(() => {
    if (message) {
      const timer = setTimeout(() => {
        setMessage(null);
      }, 4000);

      return () => clearTimeout(timer);
    }
  }, [message, setMessage]);

  if (!message) return null;

  return (
    <div
      className={`absolute top-1 px-4 py-2 gap-4 h-13 rounded-xl shadow-lg max-w-[90%] z-50 flex items-center justify-between
                  border ${borderColor} ${bgColor}`}
    >
      <p className={`${textColor} text-sm`}>{message}</p>
      <button onClick={() => setMessage(null)}>
        <X size={20} color={buttonColor} />
      </button>
    </div>
  );
}

export default ErrorMessage;
