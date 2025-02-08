import { X } from "lucide-react";

interface ErrorMessageProps {
    errorMessage: string | null;
    setErrorMessage: React.Dispatch<React.SetStateAction<string | null>>
}

function ErrorMessage({errorMessage, setErrorMessage}: ErrorMessageProps) {
    return (
        <div className="fixed flex justify-center inline-flex top-4 bg-red text-white p-2 gap-3 rounded-xl transform">
        <p>{errorMessage}</p>
        <button onClick={() => setErrorMessage(null)}>
          <X size={20}/>
        </button>
      </div>
    )
}

export default ErrorMessage;