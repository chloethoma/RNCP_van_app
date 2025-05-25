interface FormSubmitButtonProps {
  children: React.ReactNode;
}

function FormSubmitButton({ children }: FormSubmitButtonProps) {
  return (
    <button
      type="submit"
      className="w-full mt-6 px-4 py-2 text-light-grey font-bold bg-dark-green rounded-md hover:bg-dark-green-hover focus:outline-none focus:ring-2 focus:ring-dark-green focus:ring-offset-2"
    >
      {children}
    </button>
  );
}

export default FormSubmitButton;
