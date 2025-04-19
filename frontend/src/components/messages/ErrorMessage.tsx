import { X } from "lucide-react";
import { useEffect } from "react";

interface ErrorMessageProps {
  errorMessage: string | null;
  setErrorMessage: React.Dispatch<React.SetStateAction<string | null>>;
}

function ErrorMessage({ errorMessage, setErrorMessage }: ErrorMessageProps) {
  useEffect(() => {
    if (errorMessage) {
      const timer = setTimeout(() => {
        setErrorMessage(null);
      }, 4000);

      return () => clearTimeout(timer);
    }
  }, [errorMessage, setErrorMessage]);

  if (!errorMessage) return null;

  return (
    <div className="fixed top-4 w-full flex justify-center items-center bg-error-bg border border-error-border px-4 py-2 gap-1 rounded-xl shadow-lg max-w-[80vw] z-50">
      <p className="text-error-text font-bold">{errorMessage}</p>
      <button onClick={() => setErrorMessage(null)}>
        <X size={20} color="#cc0033"/>
      </button>
    </div>
  );
}

export default ErrorMessage;
