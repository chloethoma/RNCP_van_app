import clsx from "clsx";

interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  icon?: React.ReactNode;
  label?: string;
  color?: keyof typeof COLORS;
  size?: keyof typeof SIZES;
}

const COLORS = {
  darkGreen: "bg-dark-green hover:bg-dark-green-hover text-white",
  red: "bg-red hover:bg-red-hover text-white",
  white: "bg-white hover:bg-white-hover text-white",
} as const;

const SIZES = {
  default: "p-3",
  medium: "p-2",
  small: "p-1",
} as const;

function IconButton({
  icon,
  label,
  color = "darkGreen",
  size = "default",
  className,
  disabled,
  ...rest
}: ButtonProps) {
  return (
    <button
      type="button"
      disabled={disabled}
      className={clsx(
        "rounded-full shadow-sm transition flex items-center justify-center cursor-pointer whitespace-nowrap",
        COLORS[color],
        SIZES[size],
        className,
        disabled && "cursor-not-allowed opacity-60",
      )}
      {...rest}
    >
      {icon ?? (label && <span className="text-sm font-bold">{label}</span>)}
    </button>
  );
}

export default IconButton;
