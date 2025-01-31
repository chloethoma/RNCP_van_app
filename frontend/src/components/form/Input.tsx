import { useId } from "react";

/**
 * @param {string} label 
 * @returns 
 */
function FormInput({ label, type, placeHolder, value, onChange }) {
  const id = useId();
  return (
    <div className="mt-3">
      <label htmlFor={id} className="block text-sm font-medium text-dark-grey">
        {label}
      </label>

      <input
        type={type}
        id={id}
        className="mt-1 block w-full px-3 py-2 bg-light-grey border border-light-grey rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-dark-green focus:border-transparent sm:text-sm"
        placeholder={placeHolder}
        value={value}
        onChange={(e) => onChange(e.target.value)}
      />
    </div>
  );
}

export default FormInput;
