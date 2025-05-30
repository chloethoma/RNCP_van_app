import { useId } from "react";

interface FormInputProps {
  label: string;
  type: "email" | "password" | "text";
  placeholder: string;
  value: string;
  required?: boolean
  onChange: (value: string) => void;
}

function FormInput({ label, onChange, ...inputProps }: FormInputProps) {
  const id = useId();
  return (
    <div className="mt-3">
      <label htmlFor={id} className="block text-xs font-medium text-dark-grey">
        {label}
      </label>

      <input
        id={id}
        className="mt-1 block w-full px-3 py-2 text-xs bg-light-grey border border-light-grey rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-dark-green focus:border-transparent"
        {...inputProps}
        onChange={(e) => onChange(e.target.value)}
      />
    </div>
  );
}

export default FormInput;
