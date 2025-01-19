function FormButton({ value }) {
  return (
    <button
      type="submit"
      className="w-full mt-6 px-4 py-2 text-light-grey bg-dark-green rounded-md focus:outline-none focus:ring-2 focus:ring-dark focus:ring-offset-2"
    >
      {value}
    </button>
  );
}

export default FormButton;
