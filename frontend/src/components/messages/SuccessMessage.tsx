import Message from "./Message";

export default function SuccessMessage({
  successMessage,
  setSuccessMessage,
}: {
  successMessage: string | null;
  setSuccessMessage: (value: string | null) => void;
}) {
  return (
    <Message
      message={successMessage}
      setMessage={setSuccessMessage}
      borderColor="border-success-border"
      bgColor="bg-success-bg"
      textColor="text-success-text"
      buttonColor="#2e7d32"
    />
  );
}
