import Message from "./Message";

export default function ErrorMessage({ errorMessage, setErrorMessage }: {
  errorMessage: string | null;
  setErrorMessage: (value: string | null) => void;
}) {
  return (
    <Message
      message={errorMessage}
      setMessage={setErrorMessage}
      borderColor="border-error-border"
      bgColor="bg-error-bg"
      textColor="text-error-text"
      buttonColor="#cc0033"
    />
  );
}
