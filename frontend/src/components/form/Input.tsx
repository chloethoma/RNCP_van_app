import { useId } from "react";

interface FormInputProps {
  label: string;
  type: "email" | "password" | "text";
  placeHolder: string;
  value: string;
  onChange: (value: string) => void;
}



function FormInput({ label, onChange, ...inputProps }: FormInputProps) {
  const id = useId();
  return (
    <div className="mt-3">
      <label htmlFor={id} className="block text-sm font-medium text-dark-grey">
        {label}
      </label>

      <input
        id={id}
        className="mt-1 block w-full px-3 py-2 bg-light-grey border border-light-grey rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-dark-green focus:border-transparent sm:text-sm"
        {...inputProps}
        onChange={(e) => onChange(e.target.value)}
      />
    </div>
  );
}

export default FormInput;
