import clsx from "clsx";

interface ButtonProps {
  onClick?: (event: React.MouseEvent<HTMLButtonElement>) => void;
  label: string;
  color: keyof typeof COLORS;
  className?: string;
  disabled?: boolean;
}

const COLORS = {
  darkGreen: "bg-dark-green hover:bg-dark-green-hover text-white",
  red: "bg-red hover:bg-red-hover text-white",
  grey: "bg-light-grey, hover:bg-light-grey-hover text-grey",
} as const;

function ListButton({
  onClick,
  label,
  color,
  className,
  disabled = false,
}: ButtonProps) {
  return (
    <button
      onClick={onClick}
      disabled={disabled}
      className={clsx(
        "px-3 py-1 rounded-full cursor-pointer whitespace-nowrap",
        COLORS[color],
        className,
        disabled && "cursor-not-allowed opacity-60",
      )}
    >
      <span className="text-sm">{label}</span>
    </button>
  );
}

export default ListButton;
