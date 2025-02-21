import clsx from "clsx";

interface ButtonProps {
  onClick: (event: React.MouseEvent<HTMLButtonElement>) => void;
  icon?: React.ReactNode;
  label?: string;
  color?: keyof typeof COLORS;
  size?: keyof typeof SIZES;
  className?: string;
}

const COLORS = {
  "dark-green": "bg-dark-green hover:bg-dark-green-hover text-white",
  red: "bg-red hover:bg-red-hover text-white",
} as const;

const SIZES = {
  default: "p-3",
  small: "p-1",
} as const;

function Button({ onClick, icon, label, color = "dark-green", size = "default", className }: ButtonProps) {
  return (
    <button
      onClick={onClick}
      className={clsx(
        "rounded-full shadow-xl transition flex items-center justify-center",
        COLORS[color],
        SIZES[size],
        className
      )}
    >
      {icon ? icon : label && <span className="text-sm font-bold">{label}</span>}
    </button>
  );
}

export default Button;
