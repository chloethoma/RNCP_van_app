import { X } from "lucide-react";
import { useEffect } from "react";

interface ErrorMessageProps {
    errorMessage: string | null;
    setErrorMessage: React.Dispatch<React.SetStateAction<string | null>>
}

function ErrorMessage({errorMessage, setErrorMessage}: ErrorMessageProps) {
  useEffect(() => {
    if (errorMessage) {
      const timer = setTimeout(() => {
        setErrorMessage(null);
      }, 3000);

      return () => clearTimeout(timer);
    }
  }, [errorMessage, setErrorMessage]);

  if (!errorMessage) return null;

    return (
        <div className="fixed top-4 left-1/2 -translate-x-1/2 bg-red text-white px-4 py-2 flex items-center gap-3 rounded-xl shadow-lg w-fit max-w-[80vw] z-40">
        <p>{errorMessage}</p>
        <button onClick={() => setErrorMessage(null)}>
          <X size={20}/>
        </button>
      </div>
    )
}

export default ErrorMessage;