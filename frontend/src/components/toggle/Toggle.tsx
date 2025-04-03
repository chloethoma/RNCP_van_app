interface ToggleProps {
    options: { label: string; defaultValue: boolean }[];
    selectedValue: boolean;
    onChange: (value: boolean) => void;
  }
  
  function Toggle({ options, selectedValue, onChange }: ToggleProps) {
    return (
        <div className="flex bg-white px-2 py-1 rounded-full shadow-md">
          {options.map((option) => (
            <button
              key={option.label}
              onClick={() => onChange(option.defaultValue)}
              className={`px-4 py-2 text-md font-semibold rounded-full transition ${
                selectedValue === option.defaultValue ? "bg-dark-green text-white" : "text-grey"
              }`}
            >
              {option.label}
            </button>
          ))}
        </div>
    );
  }
  
  export default Toggle;
  